<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

/**
 * Application service provider.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstraps any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', $this->limitLogins(...));
    }

    /**
     * Returns the limits applied to login attempts.
     *
     * The email-only limit is deliberately independent of the IP, so it still
     * holds if the client IP is forged or rotated.
     *
     * @return Limit[]
     */
    private function limitLogins(Request $request): array
    {
        $input = $request->input('email');
        $email = Str::transliterate(Str::lower(is_string($input) ? $input : ''));
        $ip = (string) $request->ip();

        $limits = [
            Limit::perMinute(5)->by('email-ip:' . $email . '|' . $ip),
            Limit::perMinute(20)->by('ip:' . $ip),
            Limit::perHour(30)->by('email:' . $email),
        ];

        return array_map(fn (Limit $limit) => $limit->response($this->respondToThrottledLogin(...)), $limits);
    }

    /**
     * Registers any application services.
     */
    public function register(): void
    {
    }

    /**
     * Returns to the login form with an error when attempts are throttled.
     *
     * @param Request                   $request
     * @param array<string, int|string> $headers
     */
    private function respondToThrottledLogin(Request $request, array $headers): RedirectResponse
    {
        Log::warning('Login attempts throttled', ['ip' => $request->ip()]);

        $seconds = (int) ($headers['Retry-After'] ?? 60);

        return redirect()->route('login')
            ->withErrors(['email' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.'])
            ->onlyInput('email');
    }
}
