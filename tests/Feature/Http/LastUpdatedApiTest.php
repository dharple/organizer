<?php

namespace Tests\Feature\Http;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Carbon;

class LastUpdatedApiTest extends TestCase
{
    private const TOKEN = 'test-export-token';

    /**
     * Migrates a fresh in-memory database and configures the token.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->assertSuccessful();

        config(['custom.export_api.token' => self::TOKEN]);
    }

    /**
     * Returns null when there is no data.
     */
    public function testEmptyDatabase(): void
    {
        $this->getJson('/api/last-updated', ['Authorization' => 'Bearer ' . self::TOKEN])
            ->assertOk()
            ->assertExactJson(['last_updated' => null]);
    }

    /**
     * Returns the newest timestamp across all three tables.
     */
    public function testReturnsNewestAcrossEntities(): void
    {
        $box = Box::create(['label' => 'Lights']);
        $location = Location::create(['label' => 'Garage']);
        $model = BoxModel::create(['label' => 'Acme X']);

        $box->updated_at = Carbon::parse('2024-01-01 00:00:00');
        $box->saveQuietly();
        $location->updated_at = Carbon::parse('2025-06-01 12:00:00');
        $location->saveQuietly();
        $model->updated_at = Carbon::parse('2023-01-01 00:00:00');
        $model->saveQuietly();

        $response = $this->getJson('/api/last-updated', ['Authorization' => 'Bearer ' . self::TOKEN]);

        $response->assertOk();
        $this->assertSame('no-store, private', $response->headers->get('Cache-Control'));
        $this->assertTrue(Carbon::parse('2025-06-01 12:00:00')->equalTo(Carbon::parse($response->json('last_updated'))));
    }

    /**
     * Requires the bearer token.
     */
    public function testRejectsBadToken(): void
    {
        $this->getJson('/api/last-updated')->assertUnauthorized();
    }
}
