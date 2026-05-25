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
 * Validates box create and update requests.
 */
class BoxRequest extends FormRequest
{
    /**
     * All authenticated users may manage boxes.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Returns the validation rules for box data.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'box_model_id' => ['nullable', 'exists:box_models,id'],
            'description'  => ['nullable', 'string'],
            'label'        => ['required', 'string', 'max:255'],
            'location_id'  => ['nullable', 'exists:locations,id'],
        ];
    }
}
