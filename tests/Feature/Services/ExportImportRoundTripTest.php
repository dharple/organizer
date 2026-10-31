<?php

namespace Tests\Feature\Services;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use App\Services\ExportService;
use App\Services\ImportService;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

class ExportImportRoundTripTest extends TestCase
{
    /**
     * Temporary export file, removed on tear down.
     */
    private ?string $filename = null;

    /**
     * Returns the formats that support full exports.
     *
     * @return array<string, array{string}>
     */
    public static function formatProvider(): array
    {
        return [
            'json' => ['json'],
            'xml'  => ['xml'],
            'yaml' => ['yaml'],
        ];
    }

    /**
     * Migrates a fresh in-memory database.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->assertSuccessful();
    }

    /**
     * Removes the temporary export file.
     */
    protected function tearDown(): void
    {
        if ($this->filename !== null && file_exists($this->filename)) {
            unlink($this->filename);
        }

        parent::tearDown();
    }

    /**
     * Populates the database with boxes, box models, and nested locations.
     */
    private function populate(): void
    {
        $home = Location::create(['label' => 'Home', 'description' => 'Main house']);
        $garage = Location::create(['label' => 'Garage', 'parent_location_id' => $home->id]);
        $rack = Location::create(['label' => 'Wire Rack', 'parent_location_id' => $garage->id]);
        $attic = Location::create(['label' => 'Attic', 'parent_location_id' => $home->id, 'hide_from_search' => true]);
        Location::create(['label' => 'Storage Unit']);

        $tote = BoxModel::create(['label' => 'Tote', 'make' => 'Sterilite', 'model' => '1234', 'size' => '18 gal', 'color' => 'Blue']);
        $crate = BoxModel::create(['label' => 'Crate', 'make' => 'Iris', 'model' => '5678', 'size' => '12 qt', 'color' => 'Clear']);
        BoxModel::create(['label' => 'Unused Model']);

        Box::create(['label' => 'Holiday Lights', 'location_id' => $rack->id, 'box_model_id' => $tote->id]);
        Box::create(['label' => 'Old Photos', 'location_id' => $attic->id, 'box_model_id' => $crate->id]);
        Box::create(['label' => 'Tools', 'location_id' => $garage->id, 'box_model_id' => $tote->id]);
        Box::create(['label' => 'Locationless', 'box_model_id' => $crate->id]);
        Box::create(['label' => 'Modelless', 'location_id' => $home->id]);
        Box::create(['label' => 'Unassigned']);
    }

    /**
     * Captures the associations between boxes, locations, and box models.
     *
     * @return array<string, array<int|string, mixed>>
     */
    private function snapshot(): array
    {
        return [
            'boxes' => Box::orderBy('box_number')->get()
                ->mapWithKeys(fn(Box $box) => [$box->box_number => [
                    'id'           => $box->id,
                    'label'        => $box->label,
                    'box_model_id' => $box->box_model_id,
                    'box_model'    => $box->boxModel?->label,
                    'location_id'  => $box->location_id,
                    'location'     => $box->location?->getDisplayLabel(),
                ]])
                ->all(),
            'box_models' => BoxModel::orderBy('id')->get()
                ->mapWithKeys(fn(BoxModel $boxModel) => [$boxModel->id => [
                    'label' => $boxModel->label,
                    'boxes' => $boxModel->boxes()->orderBy('box_number')->pluck('label')->all(),
                ]])
                ->all(),
            'locations' => Location::orderBy('id')->get()
                ->mapWithKeys(fn(Location $location) => [$location->id => [
                    'display_label'      => $location->getDisplayLabel(),
                    'hide_from_search'   => $location->hide_from_search,
                    'parent_location_id' => $location->parent_location_id,
                    'boxes'              => $location->boxes()->orderBy('box_number')->pluck('label')->all(),
                ]])
                ->all(),
        ];
    }

    /**
     * Exports, swaps in an empty database, then imports the export.
     */
    private function roundTrip(string $format): void
    {
        $response = (new ExportService())->export(['format' => $format, 'type' => 'full']);

        $this->filename = $response->getFilename();

        // Discard the in-memory database and migrate a brand new one.
        DB::purge();
        $this->artisan('migrate')->assertSuccessful();
        $this->assertSame(0, Box::count() + BoxModel::count() + Location::count());

        ob_start();
        try {
            (new ImportService())->import(['filename' => $this->filename, 'format' => $format]);
        } finally {
            ob_end_clean();
        }
    }

    /**
     * Tests that box to box model associations survive an export and import.
     */
    #[DataProvider('formatProvider')]
    public function testBoxModelAssociationsArePreserved(string $format): void
    {
        $this->populate();
        $expected = $this->snapshot();

        $this->roundTrip($format);

        $actual = $this->snapshot();
        $this->assertSame($expected['box_models'], $actual['box_models']);
        $this->assertSame(
            array_column($expected['boxes'], 'box_model', 'label'),
            array_column($actual['boxes'], 'box_model', 'label'),
        );
    }

    /**
     * Tests that box to location associations survive an export and import.
     */
    #[DataProvider('formatProvider')]
    public function testBoxLocationAssociationsArePreserved(string $format): void
    {
        $this->populate();
        $expected = $this->snapshot();

        $this->roundTrip($format);

        $actual = $this->snapshot();
        $this->assertSame(
            array_column($expected['boxes'], 'location', 'label'),
            array_column($actual['boxes'], 'location', 'label'),
        );
        $this->assertSame(
            array_column($expected['locations'], 'boxes', 'display_label'),
            array_column($actual['locations'], 'boxes', 'display_label'),
        );
    }

    /**
     * Tests that the location hierarchy survives an export and import.
     */
    #[DataProvider('formatProvider')]
    public function testLocationHierarchyIsPreserved(string $format): void
    {
        $this->populate();
        $expected = $this->snapshot();

        $this->roundTrip($format);

        $this->assertSame($expected['locations'], $this->snapshot()['locations']);
        $this->assertSame(
            'Home - Garage - Wire Rack',
            Box::where('label', 'Holiday Lights')->firstOrFail()->location->getDisplayLabel(),
        );
    }

    /**
     * Tests that the full set of records and IDs is identical after an export and import.
     */
    #[DataProvider('formatProvider')]
    public function testRoundTripIsLossless(string $format): void
    {
        $this->populate();
        $expected = $this->snapshot();

        $this->roundTrip($format);

        $this->assertSame($expected, $this->snapshot());
    }
}
