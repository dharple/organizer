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

use App\Http\Requests\BoxModelRequest;
use App\Models\BoxModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Handles box model CRUD operations.
 */
class BoxModelController extends Controller
{
    /**
     * Displays the create box model form.
     */
    public function create(): View
    {
        return view('box-model.edit', [
            'entity' => new BoxModel(),
        ]);
    }

    /**
     * Displays the edit box model form.
     */
    public function edit(int $id): View|RedirectResponse
    {
        $entity = BoxModel::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to find box model ' . $id);
        }

        return view('box-model.edit', [
            'entity' => $entity,
        ]);
    }

    /**
     * Displays all box models.
     */
    public function index(): View
    {
        return view('box-model.index', [
            'boxModels' => BoxModel::orderBy('label')->get(),
        ]);
    }

    /**
     * Stores a newly created box model.
     */
    public function store(BoxModelRequest $request): RedirectResponse
    {
        $entity = BoxModel::create($request->validated());

        return redirect()->route('box-model.index')
            ->with('success', 'Created ' . $entity->getDisplayLabel());
    }

    /**
     * Updates an existing box model.
     */
    public function update(BoxModelRequest $request, int $id): RedirectResponse
    {
        $entity = BoxModel::find($id);
        if (!$entity) {
            return redirect()->route('home')->with('error', 'Unable to find box model ' . $id);
        }

        $entity->fill($request->validated());
        $entity->save();

        return redirect()->route('box-model.index')
            ->with('success', 'Updated ' . $entity->getDisplayLabel());
    }
}
