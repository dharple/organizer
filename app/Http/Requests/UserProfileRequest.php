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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * Validates user password change requests.
 */
class UserProfileRequest extends FormRequest
{
    /**
     * All authenticated users may update their own profile.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Returns the validation rules for password change data.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('Password does not match our records');
                    }
                },
            ],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ];
    }
}
