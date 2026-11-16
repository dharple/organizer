<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\TestCase;

class ContentSecurityPolicyTest extends TestCase
{
    private const ENFORCE = 'Content-Security-Policy';

    private const REPORT_ONLY = 'Content-Security-Policy-Report-Only';

    /**
     * Migrates a fresh in-memory database and enforces the policy.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate')->assertSuccessful();

        config(['custom.csp.report_only' => false]);
    }

    /**
     * Leaves API responses without a policy.
     */
    public function testApiHasNoPolicy(): void
    {
        $response = $this->getJson('/api/export');

        $response->assertHeaderMissing(self::ENFORCE);
        $response->assertHeaderMissing(self::REPORT_ONLY);
    }

    /**
     * Enforces the configured policy on web pages.
     */
    public function testEnforcesPolicyOnWebPages(): void
    {
        $response = $this->get('/login')->assertOk();

        $response->assertHeaderMissing(self::REPORT_ONLY);
        $policy = (string) $response->headers->get(self::ENFORCE);

        $this->assertStringContainsString("default-src 'self'", $policy);
        $this->assertStringContainsString("script-src 'self' https://cdnjs.cloudflare.com", $policy);
        $this->assertStringContainsString("frame-ancestors 'none'", $policy);
        $this->assertStringNotContainsString('unsafe-inline', $policy);
    }

    /**
     * Sends the report-only header instead when configured.
     */
    public function testReportOnlyMode(): void
    {
        config(['custom.csp.report_only' => true]);

        $response = $this->get('/login')->assertOk();

        $response->assertHeaderMissing(self::ENFORCE);
        $response->assertHeader(self::REPORT_ONLY);
    }

    /**
     * Applies the policy to redirects from the web group too.
     */
    public function testPolicyOnRedirects(): void
    {
        $this->get('/')->assertRedirect()->assertHeader(self::ENFORCE);
    }
}
