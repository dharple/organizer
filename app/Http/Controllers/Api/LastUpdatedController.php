<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

/**
 * Reports the most recent update time across all entities.
 */
class LastUpdatedController extends Controller
{
    /**
     * Returns the newest updated_at among boxes, box models, and locations.
     *
     * Soft-deleted rows are included, since deleting a row touches updated_at.
     * The value is null when there are no rows at all.
     */
    public function __invoke(): JsonResponse
    {
        $latest = null;

        foreach ([Box::class, BoxModel::class, Location::class] as $class) {
            $value = $class::withTrashed()->max('updated_at');
            if ($value === null) {
                continue;
            }

            $time = Carbon::parse($value);
            if ($latest === null || $time->greaterThan($latest)) {
                $latest = $time;
            }
        }

        $response = response()->json(['last_updated' => $latest?->toIso8601String()]);
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
