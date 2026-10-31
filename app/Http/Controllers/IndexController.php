<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use Illuminate\View\View;

/**
 * Handles the home and about pages.
 */
class IndexController extends Controller
{
    /**
     * Displays the about page.
     */
    public function about(): View
    {
        return view('about');
    }

    /**
     * Displays the home dashboard.
     */
    public function index(): View
    {
        $allLocations = Location::allWithParents();

        $locationIdsWithBoxes = Box::whereNotNull('location_id')
            ->distinct()
            ->pluck('location_id');

        $locations = $allLocations->only($locationIdsWithBoxes->all())
            ->reject(fn(Location $l) => $l->hide_from_search)
            ->sortBy(fn(Location $l) => $l->getDisplayLabel())
            ->values();

        $recentBoxes = Box::loadDisplayRelations(
            Box::recent('-1 week')->limit(3)->get(),
            $allLocations
        );

        // Boxes for each location are loaded on demand; see LocationController::boxes().
        return view('home', [
            'boxCount'      => Box::count(),
            'boxModelCount' => BoxModel::count(),
            'locationCount' => $allLocations->count(),
            'locations'     => $locations,
            'recentBoxes'   => $recentBoxes,
        ]);
    }
}
