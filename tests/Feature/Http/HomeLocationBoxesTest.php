<?php

namespace Tests\Feature\Http;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

class HomeLocationBoxesTest extends TestCase
{
    /**
     * The authenticated user for requests.
     */
    private User $user;

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
     * The boxes endpoint returns 404 for an unknown location.
     */
    public function testBoxesEndpointMissingLocation(): void
    {
        $this->actingAs($this->user)->get('/location/999/boxes')->assertNotFound();
    }

    /**
     * The boxes endpoint renders box cards for the location only.
     */
    public function testBoxesEndpointRendersBoxes(): void
    {
        $model = BoxModel::create(['label' => 'Sterilite 18qt']);
        $garage = Location::create(['label' => 'Garage']);
        $attic = Location::create(['label' => 'Attic']);
        Box::create(['label' => 'Holiday Lights', 'location_id' => $garage->id, 'box_model_id' => $model->id]);
        Box::create(['label' => 'Old Taxes', 'location_id' => $attic->id]);

        $this->actingAs($this->user)
            ->get('/location/' . $garage->id . '/boxes')
            ->assertOk()
            ->assertSee('Holiday Lights')
            ->assertSee('Sterilite 18qt')
            ->assertDontSee('Old Taxes')
            ->assertDontSee('<html', false);
    }

    /**
     * An unauthenticated AJAX request gets a 401 rather than a login redirect.
     */
    public function testBoxesEndpointUnauthenticatedAjax(): void
    {
        $location = Location::create(['label' => 'Garage']);

        $this->get('/location/' . $location->id . '/boxes', ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertUnauthorized();

        $this->get('/location/' . $location->id . '/boxes')->assertRedirect('/login');
    }

    /**
     * The home page lists locations with full labels but does not render their boxes.
     */
    public function testHomeDefersBoxes(): void
    {
        $home = Location::create(['label' => 'Home']);
        $garage = Location::create(['label' => 'Garage', 'parent_location_id' => $home->id]);
        $hidden = Location::create(['label' => 'Secret Stash', 'hide_from_search' => true]);
        Location::create(['label' => 'Empty Shelf']);
        $box = Box::create(['label' => 'Holiday Lights', 'location_id' => $garage->id]);
        Box::create(['label' => 'Hidden Box', 'location_id' => $hidden->id]);

        // Keep boxes out of the "recently changed" section.
        Box::query()->update(['updated_at' => now()->subMonth()]);

        $this->actingAs($this->user)
            ->get('/')
            ->assertOk()
            ->assertSee('Home - Garage')
            ->assertSee(route('location.boxes', ['id' => $garage->id]))
            ->assertDontSee($box->label)
            ->assertDontSee('Secret Stash')
            ->assertDontSee('Empty Shelf');
    }

    /**
     * The home page query count does not grow with the number of locations or boxes.
     */
    public function testHomeQueryCountIsConstant(): void
    {
        $count = function (): int {
            $this->actingAs($this->user);
            DB::flushQueryLog();
            DB::enableQueryLog();
            $this->get('/')->assertOk();
            $queries = count(DB::getQueryLog());
            DB::disableQueryLog();

            return $queries;
        };

        $parent = Location::create(['label' => 'Home']);
        $location = Location::create(['label' => 'Garage', 'parent_location_id' => $parent->id]);
        Box::create(['label' => 'Box 1', 'location_id' => $location->id]);
        $baseline = $count();

        for ($i = 0; $i < 5; $i++) {
            $location = Location::create(['label' => 'Shelf ' . $i, 'parent_location_id' => $location->id]);
            Box::create(['label' => 'Box A' . $i, 'location_id' => $location->id]);
            Box::create(['label' => 'Box B' . $i, 'location_id' => $location->id]);
        }

        $this->assertSame($baseline, $count());
    }
}
