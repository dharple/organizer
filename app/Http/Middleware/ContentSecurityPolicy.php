<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds the configured Content-Security-Policy header to web responses.
 */
class ContentSecurityPolicy
{
    /**
     * Joins directive names and their sources into a policy string.
     *
     * @param array<string, list<string>> $directives
     */
    private function buildPolicy(array $directives): string
    {
        $parts = [];
        foreach ($directives as $name => $sources) {
            $parts[] = trim($name . ' ' . implode(' ', $sources));
        }

        return implode('; ', $parts);
    }

    /**
     * Sets the policy header, or its report-only variant, on the response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $directives = (array) config('custom.csp.directives', []);

        // A fresh nonce on every response lets Cloudflare tag the inline
        // JavaScript Detections script it injects; Cloudflare reads the
        // nonce from this header.
        if (isset($directives['script-src'])) {
            $directives['script-src'][] = "'nonce-" . base64_encode(random_bytes(16)) . "'";
        }

        $policy = $this->buildPolicy($directives);
        if ($policy === '') {
            return $response;
        }

        $header = 'Content-Security-Policy';
        if (config('custom.csp.report_only')) {
            $header .= '-Report-Only';
        }

        $response->headers->set($header, $policy);

        return $response;
    }
}
