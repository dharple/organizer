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
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Creates a new application user.
 */
class UserAddCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a user';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add {email : Email address} {password : Password}';

    /**
     * Executes the command.
     */
    public function handle(): int
    {
        $user = new User();
        $user->email    = $this->argument('email');
        $user->roles    = ['ROLE_USER'];
        $user->password = Hash::make($this->argument('password'));
        $user->save();

        $this->info('created user: ' . $user->email);

        return self::SUCCESS;
    }
}
