<?php

namespace Tests\Feature\Http;

use App\Models\Box;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;

class ExportApiTest extends TestCase
{
    private const TOKEN = 'test-export-token';

    /**
     * Returns header sets that must be rejected.
     *
     * @return array<string, array{array<string, string>}>
     */
    public static function badHeaderProvider(): array
    {
        return [
            'missing' => [[]],
            'wrong'   => [['Authorization' => 'Bearer not-the-token']],
            'basic'   => [['Authorization' => 'Basic ' . base64_encode('user:' . self::TOKEN)]],
        ];
    }

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
     * Rejects requests without the configured bearer token.
     *
     * @param array<string, string> $headers
     */
    #[DataProvider('badHeaderProvider')]
    public function testRejectsBadToken(array $headers): void
    {
        $this->get('/api/export?type=full&format=yaml', $headers)->assertUnauthorized();
    }

    /**
     * Rejects every request when no token is configured.
     */
    public function testRejectsWhenTokenNotConfigured(): void
    {
        config(['custom.export_api.token' => null]);

        $this->get('/api/export?type=full&format=yaml', ['Authorization' => 'Bearer '])
            ->assertUnauthorized();
    }

    /**
     * Returns a JSON validation error, not a redirect, for bad parameters.
     */
    public function testRejectsInvalidParameters(): void
    {
        $this->get('/api/export?type=bogus&format=yaml', ['Authorization' => 'Bearer ' . self::TOKEN])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Returns the export when the token matches.
     */
    public function testReturnsExport(): void
    {
        Box::create(['label' => 'Holiday Lights']);

        $response = $this->get('/api/export?type=full&format=yaml', ['Authorization' => 'Bearer ' . self::TOKEN]);

        $response->assertOk();

        $data = Yaml::parse($response->streamedContent());
        $this->assertSame('Holiday Lights', $data['boxes'][0]['label']);
    }
}
