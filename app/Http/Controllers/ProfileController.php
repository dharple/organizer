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

use App\Http\Requests\UserProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Handles user profile management.
 */
class ProfileController extends Controller
{
    /**
     * Displays the profile edit form.
     */
    public function edit(): View
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Updates the user's password.
     */
    public function update(UserProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->password = Hash::make($request->validated('password'));
        $user->save();

        return redirect()->route('home')->with('success', 'Password Updated');
    }
}
