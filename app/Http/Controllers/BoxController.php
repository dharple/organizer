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

use App\Http\Requests\BoxRequest;
use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Handles box CRUD and search operations.
 */
class BoxController extends Controller
{
    /**
     * Session key used to detect a freshly created box.
     */
    protected const SESSION_NEW_BOX = 'nübox';

    /**
     * Displays the create box form.
     */
    public function create(): View
    {
        return view('box.edit', [
            'boxModels' => BoxModel::orderBy('label')->get(),
            'entity'    => new Box(),
            'locations' => Location::all()->sortBy(fn(Location $l) => $l->getDisplayLabel())->values(),
        ]);
    }

    /**
     * Displays the edit box form.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $entity = Box::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to find box ' . $id);
        }

        return view('box.edit', [
            'boxModels' => BoxModel::orderBy('label')->get(),
            'entity'    => $entity,
            'locations' => Location::all()->sortBy(fn(Location $l) => $l->getDisplayLabel())->values(),
        ]);
    }

    /**
     * Displays all boxes.
     */
    public function index(): View
    {
        return view('box.index', [
            'boxes' => Box::sortedByDisplayLabel()->get(),
            'title' => 'All Boxes',
        ]);
    }

    /**
     * Displays recently changed boxes.
     */
    public function recent(): View
    {
        return view('box.index', [
            'boxes' => Box::recent('-30 days')->get(),
            'title' => 'Boxes Added or Changed Over the Past 30 Days',
        ]);
    }

    /**
     * Displays keyword search results.
     */
    public function search(Request $request): View
    {
        $query = $request->get('q');
        $boxes = $this->findByKeyword($query);

        return view('box.search', [
            'boxes' => $boxes,
            'query' => $query,
            'type'  => null,
        ]);
    }

    /**
     * Displays boxes for a given location.
     */
    public function searchByLocation(int $id): View|RedirectResponse
    {
        $entity = Location::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to show boxes for invalid location ' . $id);
        }

        return view('box.search', [
            'boxes'  => $entity->boxes,
            'entity' => $entity,
            'type'   => 'Location',
        ]);
    }

    /**
     * Displays boxes for a given box model.
     */
    public function searchByModel(int $id): View|RedirectResponse
    {
        $entity = BoxModel::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to show boxes for invalid box model ' . $id);
        }

        return view('box.search', [
            'boxes'  => $entity->boxes,
            'entity' => $entity,
            'type'   => 'BoxModel',
        ]);
    }

    /**
     * Displays a single box.
     */
    public function show(Request $request, int $id): View|RedirectResponse
    {
        $entity = Box::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to show box ' . $id);
        }

        $hideMessage = $request->session()->get(static::SESSION_NEW_BOX, false);
        $request->session()->forget(static::SESSION_NEW_BOX);

        return view('box.search', [
            'boxes'       => [$entity],
            'entity'      => $entity,
            'hideMessage' => $hideMessage,
            'type'        => 'Box',
        ]);
    }

    /**
     * Stores a newly created box.
     */
    public function store(BoxRequest $request): RedirectResponse
    {
        $box = new Box();
        $box->fill($request->validated());
        $box->save();

        $request->session()->put(static::SESSION_NEW_BOX, true);

        return redirect()->route('box.show', ['id' => $box->id])
            ->with('success', 'Box created successfully.');
    }

    /**
     * Updates an existing box.
     */
    public function update(BoxRequest $request, int $id): RedirectResponse
    {
        $entity = Box::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to find box ' . $id);
        }

        $entity->fill($request->validated());
        $entity->save();

        return redirect()->route('box.recent')
            ->with('success', 'Updated ' . $entity->getDisplayLabel());
    }

    /**
     * Searches boxes by keyword across label, description, location, and box model.
     *
     * @return array<Box>
     */
    protected function findByKeyword(?string $keyword): array
    {
        if (empty($keyword)) {
            return [];
        }

        $keywords = preg_split('/[\s,]+/', trim($keyword)) ?: [$keyword];
        $single = count($keywords) === 1;
        if (!$single) {
            $keywords[] = $keyword;
        }

        $output = [];
        $numeric = false;

        foreach ($keywords as $kw) {
            if (is_numeric($kw)) {
                $numeric = true;
                $found = Box::where('box_number', ltrim($kw, '0'))->get();
                foreach ($found as $box) {
                    $output[] = $box;
                }
            }

            $matchingLocationIds = Location::all()
                ->filter(fn(Location $l) => stripos($l->getDisplayLabel(), $kw) !== false)
                ->pluck('id')
                ->toArray();

            $found = Box::where(function ($q) use ($kw, $matchingLocationIds) {
                $q->where('label', 'like', '%' . $kw . '%')
                    ->orWhere('description', 'like', '%' . $kw . '%')
                    ->orWhereIn('location_id', $matchingLocationIds)
                    ->orWhereHas('boxModel', fn($q) => $q->where('label', 'like', '%' . $kw . '%'));
            })->get();

            foreach ($found as $box) {
                $output[] = $box;
            }
        }

        $all = [];
        $counts = [];

        foreach ($output as $box) {
            $exactMatch = ($single && $numeric && $box->box_number == $keyword);

            if ($box->isHidden() && !$exactMatch) {
                continue;
            }

            $id = $box->box_number;
            if (!isset($counts[$id])) {
                $all[$id] = $box;
                $counts[$id] = 1;
            } else {
                $counts[$id]++;
            }

            if ($exactMatch) {
                $counts[$id] += 10;
            }
        }

        array_walk($counts, function (&$value, $key) {
            $value = sprintf('%d%04d', 100 - $value, $key);
        });

        asort($counts);

        $ret = [];
        foreach ($counts as $id => $count) {
            $ret[] = $all[$id];
        }

        return $ret;
    }
}
