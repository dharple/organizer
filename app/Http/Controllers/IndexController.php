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
        $locations = Location::whereIn('id', function ($query) {
            $query->selectRaw('DISTINCT location_id')
                ->from('box')
                ->whereNull('box.deleted_at')
                ->whereNotNull('location_id');
        })
            ->where('hide_from_search', false)
            ->get()
            ->sortBy(fn(Location $l) => $l->getDisplayLabel())
            ->values();

        return view('home', [
            'boxCount'      => Box::count(),
            'boxModelCount' => BoxModel::count(),
            'locationCount' => Location::count(),
            'locations'     => $locations,
            'recentBoxes'   => Box::recent('-1 week')->limit(3)->get(),
        ]);
    }
}
