<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class ApiRouteDisclosureTest extends TestCase
{
    private const TOKEN = 'test-export-token';

    /**
     * Provides requests for known and unknown routes.
     *
     * @return array<string, array{string, string}>
     */
    public static function unauthenticatedRequests(): array
    {
        return [
            'known route'           => ['GET', '/api/export'],
            'known route, bad verb' => ['POST', '/api/export'],
            'known route, delete'   => ['DELETE', '/api/last-updated'],
            'unknown route'         => ['GET', '/api/blah'],
            'unknown route, post'   => ['POST', '/api/blah'],
            'unknown nested route'  => ['GET', '/api/blah/blah'],
        ];
    }

    /**
     * Configures the token and renders errors as production does.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.debug' => false, 'custom.export_api.token' => self::TOKEN]);
    }

    /**
     * Reports unknown routes as missing once the caller has authenticated.
     */
    public function testAuthenticatedUnknownRouteIsNotFound(): void
    {
        $this->getJson('/api/blah', ['Authorization' => 'Bearer ' . self::TOKEN])
            ->assertNotFound()
            ->assertExactJson(['message' => 'Not Found']);
    }

    /**
     * Rejects every unauthenticated request the same way, whether or not the route exists.
     */
    #[DataProvider('unauthenticatedRequests')]
    public function testUnauthenticatedResponseDoesNotRevealRoutes(string $method, string $uri): void
    {
        $this->json($method, $uri)
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthorized']);
    }

    /**
     * Counts unknown routes against the same throttle as known routes.
     */
    public function testUnknownRoutesShareTheThrottle(): void
    {
        foreach (range(1, 5) as $ignored) {
            $this->getJson('/api/blah')->assertUnauthorized();
        }

        $this->getJson('/api/export')->assertTooManyRequests();
    }
}
