<?php

namespace Tests\Feature\Http;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Pages that list locations or box models should not issue more queries as
 * locations, hierarchy depth, or boxes grow.
 */
class LocationQueryCountTest extends TestCase
{
    /**
     * The authenticated user for requests.
     */
    private User $user;

    /**
     * Returns URLs that list every location, keyed by page name.
     *
     * The URL is a closure because some pages need an entity ID.
     *
     * @return array<string, array{Closure(): string}>
     */
    public static function pageProvider(): array
    {
        return [
            'box create'      => [fn() => '/box/new'],
            'box edit'        => [fn() => '/box/' . Box::firstOrFail()->id],
            'location create' => [fn() => '/location/new'],
            'location edit'   => [fn() => '/location/' . Location::firstOrFail()->id],
            'location index'  => [fn() => '/location/showAll'],
            'box model index' => [fn() => '/box/model/showAll'],
            'search'          => [fn() => '/box/search?q=nomatch'],
        ];
    }

    /**
     * Migrates a fresh in-memory database and creates a user.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->assertSuccessful();

        $this->user = User::create(['email' => 'test@example.com', 'password' => 'secret']);
    }

    /**
     * Adds a chain of nested locations, each holding boxes of one model.
     */
    private function addLocations(int $depth, int $boxesEach): void
    {
        $model = BoxModel::create(['label' => 'Model ' . uniqid()]);
        $parent = null;

        for ($i = 0; $i < $depth; $i++) {
            $parent = Location::create(['label' => 'Shelf ' . uniqid(), 'parent_location_id' => $parent?->id]);
            for ($j = 0; $j < $boxesEach; $j++) {
                Box::create(['label' => 'Box ' . uniqid(), 'location_id' => $parent->id, 'box_model_id' => $model->id]);
            }
        }
    }

    /**
     * Counts the queries issued by a GET request.
     */
    private function countQueries(string $url): int
    {
        $this->actingAs($this->user);
        DB::flushQueryLog();
        DB::enableQueryLog();
        $this->get($url)->assertOk();
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $queries;
    }

    /**
     * Box counts on the index pages exclude soft-deleted boxes.
     */
    public function testBoxCountsExcludeDeleted(): void
    {
        $model = BoxModel::create(['label' => 'Sterilite 18qt']);
        $location = Location::create(['label' => 'Garage']);
        Box::create(['label' => 'Kept', 'location_id' => $location->id, 'box_model_id' => $model->id]);
        Box::create(['label' => 'Gone', 'location_id' => $location->id, 'box_model_id' => $model->id])->delete();

        $this->actingAs($this->user);
        $this->get('/location/showAll')->assertSee('1 Box</a>', false)->assertDontSee('2 Boxes');
        $this->get('/box/model/showAll')->assertSee('1 Box</a>', false)->assertDontSee('2 Boxes');
    }

    /**
     * The location:list command reports full labels and box counts.
     */
    public function testLocationListCommand(): void
    {
        $home = Location::create(['label' => 'Home']);
        $garage = Location::create(['label' => 'Garage', 'parent_location_id' => $home->id]);
        Box::create(['label' => 'One', 'location_id' => $garage->id]);
        Box::create(['label' => 'Two', 'location_id' => $garage->id]);

        $this->artisan('location:list')
            ->expectsTable(['id', 'label', 'boxes'], [
                [$home->id, 'Home', 0],
                [$garage->id, 'Home - Garage', 2],
            ])
            ->assertSuccessful();
    }

    /**
     * Each page's query count stays flat as data grows.
     *
     * @param Closure(): string $url
     */
    #[DataProvider('pageProvider')]
    public function testQueryCountIsConstant(Closure $url): void
    {
        $this->addLocations(2, 1);
        $baseline = $this->countQueries($url());

        $this->addLocations(5, 3);
        $this->assertSame($baseline, $this->countQueries($url()));
    }

    /**
     * Keyword search still matches boxes by an ancestor's label.
     */
    public function testSearchMatchesAncestorLabel(): void
    {
        $home = Location::create(['label' => 'Cottage']);
        $garage = Location::create(['label' => 'Garage', 'parent_location_id' => $home->id]);
        Box::create(['label' => 'Holiday Lights', 'location_id' => $garage->id]);

        $this->actingAs($this->user)
            ->get('/box/search?q=cottage')
            ->assertOk()
            ->assertSee('Holiday Lights');
    }
}
