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
 * Requires a bearer token matching the configured export API token.
 */
class VerifyExportToken
{
    /**
     * Rejects the request unless it carries the configured bearer token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('custom.export_api.token');
        $provided = (string) $request->bearerToken();

        if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
            abort(401);
        }

        return $next($request);
    }
}
