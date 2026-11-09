<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Creates a new application user.
 */
#[Description('Add a user')]
#[Signature('user:add {email : Email address}')]
class UserAddCommand extends Command
{
    /**
     * Executes the command.
     */
    public function handle(): int
    {
        $user = new User();
        $user->email    = $this->argument('email');
        $user->roles    = ['ROLE_USER'];

        $password = (string) $this->secret('Password');
        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters.');
            return self::FAILURE;
        }
        $user->password = Hash::make($password);

        $user->save();

        $this->info('created user: ' . $user->email);

        return self::SUCCESS;
    }
}
