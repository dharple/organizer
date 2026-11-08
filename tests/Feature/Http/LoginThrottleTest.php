<?php

namespace Tests\Feature\Http;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;

class LoginThrottleTest extends TestCase
{
    private const THROTTLED = 'Too many login attempts';

    /**
     * Sends a login attempt with a wrong password from the given IP.
     *
     * @param string                $email
     * @param string                $remoteAddr
     * @param array<string, string> $headers
     */
    private function attempt(string $email, string $remoteAddr = '203.0.113.10', array $headers = []): TestResponse
    {
        return $this->withServerVariables(['REMOTE_ADDR' => $remoteAddr])
            ->post('/login', ['email' => $email, 'password' => 'wrong'], $headers);
    }

    /**
     * Asserts that the response redirected with the throttled message.
     */
    private function assertThrottled(TestResponse $response): void
    {
        $response->assertRedirect(route('login'));
        $this->followRedirects($response)->assertOk()->assertSee(self::THROTTLED);
    }

    /**
     * Asserts that the response was handled as a normal failed login.
     */
    private function assertNotThrottled(TestResponse $response): void
    {
        $response->assertSessionHasErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
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
     * Clears the Laravel Cloud marker set by some tests.
     */
    protected function tearDown(): void
    {
        unset($_SERVER['LARAVEL_CLOUD']);

        parent::tearDown();
    }

    /**
     * Doesn't let a forged X-Forwarded-For evade the limit on Laravel Cloud.
     */
    public function testForgedForwardedForDoesNotEvadeLimitOnCloud(): void
    {
        $_SERVER['LARAVEL_CLOUD'] = '1';

        for ($i = 1; $i <= 5; $i++) {
            $this->assertNotThrottled($this->attempt('user@example.com', '10.0.0.1', [
                'CF-Connecting-IP' => '198.51.100.7',
                'X-Forwarded-For'  => '192.0.2.' . $i,
            ]));
        }

        $this->assertThrottled($this->attempt('user@example.com', '10.0.0.1', [
            'CF-Connecting-IP' => '198.51.100.7',
            'X-Forwarded-For'  => '192.0.2.99',
        ]));
    }

    /**
     * Limits one email across any number of IPs.
     */
    public function testThrottlesEmailAcrossIps(): void
    {
        for ($i = 1; $i <= 30; $i++) {
            $this->assertNotThrottled($this->attempt('user@example.com', '203.0.113.' . $i));
        }

        $this->assertThrottled($this->attempt('user@example.com', '203.0.113.200'));
    }

    /**
     * Limits one email from one IP, regardless of the email's case.
     */
    public function testThrottlesEmailFromOneIp(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->assertNotThrottled($this->attempt('user@example.com'));
        }

        $this->assertThrottled($this->attempt('USER@example.com'));
        $this->assertNotThrottled($this->attempt('user@example.com', '203.0.113.11'));
    }

    /**
     * Limits one IP across many emails.
     */
    public function testThrottlesIpAcrossEmails(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->assertNotThrottled($this->attempt('user' . $i . '@example.com'));
        }

        $this->assertThrottled($this->attempt('another@example.com'));
    }

    /**
     * Rejects an array in place of the email without an error.
     */
    public function testNonStringEmailIsHandled(): void
    {
        $this->post('/login', ['email' => ['a@example.com'], 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
    }
}
