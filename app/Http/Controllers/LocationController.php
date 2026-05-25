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

use App\Http\Requests\LocationRequest;
use App\Models\Location;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Handles location CRUD operations.
 */
class LocationController extends Controller
{
    /**
     * Displays the create location form.
     */
    public function create(): View
    {
        return view('location.edit', [
            'entity'    => new Location(),
            'locations' => Location::all()->sortBy(fn(Location $l) => $l->getDisplayLabel())->values(),
        ]);
    }

    /**
     * Displays the edit location form.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $entity = Location::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to find location ' . $id);
        }

        return view('location.edit', [
            'entity'    => $entity,
            'locations' => Location::all()->sortBy(fn(Location $l) => $l->getDisplayLabel())->values(),
        ]);
    }

    /**
     * Displays all locations.
     */
    public function index(): View
    {
        return view('location.index', [
            'locations' => Location::all()->sortBy(fn(Location $l) => $l->getDisplayLabel())->values(),
        ]);
    }

    /**
     * Stores a newly created location.
     */
    public function store(LocationRequest $request): RedirectResponse
    {
        $entity = new Location();
        $entity->fill($request->safe()->except('parent_location_id'));

        if ($request->filled('parent_location_id')) {
            $parent = Location::find($request->input('parent_location_id'));
            if ($parent) {
                try {
                    $entity->setParentLocation($parent);
                } catch (Exception $e) {
                    return back()->withErrors(['parent_location_id' => $e->getMessage()]);
                }
            }
        }

        $entity->save();

        return redirect()->route('location.index')
            ->with('success', 'Created ' . $entity->getDisplayLabel());
    }

    /**
     * Updates an existing location.
     */
    public function update(LocationRequest $request, int $id): RedirectResponse
    {
        $entity = Location::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to find location ' . $id);
        }

        $entity->fill($request->safe()->except('parent_location_id'));

        $parentId = $request->input('parent_location_id');
        if ($parentId) {
            $parent = Location::find($parentId);
            if ($parent) {
                try {
                    $entity->setParentLocation($parent);
                } catch (Exception $e) {
                    return back()->withErrors(['parent_location_id' => $e->getMessage()]);
                }
            }
        } else {
            $entity->parent_location_id = null;
            $entity->unsetRelation('parentLocation');
        }

        $entity->save();

        return redirect()->route('location.index')
            ->with('success', 'Updated ' . $entity->getDisplayLabel());
    }
}
