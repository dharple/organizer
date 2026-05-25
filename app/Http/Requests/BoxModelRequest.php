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
 * Validates box model create and update requests.
 */
class BoxModelRequest extends FormRequest
{
    /**
     * All authenticated users may manage box models.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Returns the validation rules for box model data.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'color' => ['nullable', 'string', 'max:64'],
            'label' => ['required', 'string', 'max:255'],
            'latch' => ['nullable', 'string', 'max:16'],
            'make'  => ['nullable', 'string', 'max:64'],
            'model' => ['nullable', 'string', 'max:64'],
            'size'  => ['nullable', 'string', 'max:64'],
        ];
    }
}
