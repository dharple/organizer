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
 * Validates data export requests.
 */
class ExportRequest extends FormRequest
{
    /**
     * All authenticated users may export data.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Returns the validation rules for export parameters.
     *
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'format' => ['required', 'in:csv,json,ods,xlsx,xml,yaml'],
            'type'   => ['required', 'in:full,simple'],
        ];
    }
}
