<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validates location create and update requests.
 */
class LocationRequest extends FormRequest
{
    /**
     * All authenticated users may manage locations.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Returns the validation rules for location data.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'description'        => ['nullable', 'string'],
            'hide_from_search'   => ['nullable', 'boolean'],
            'label'              => ['required', 'string', 'max:255'],
            'parent_location_id' => ['nullable', 'exists:location,id'],
        ];
    }
}
