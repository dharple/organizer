<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

use App\Support\Gravatar;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Represents an application user.
 *
 * @property int     $id
 * @property string  $email
 * @property string  $password
 */
#[Fillable([
    'email',
    'password',
    'roles',
])]
#[Hidden([
    'password',
    'remember_token',
])]
#[Table(name: 'user')]
class User extends Authenticatable
{
    use SoftDeletes;

    /**
     * Whether the model uses created_at and updated_at columns.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Returns the attribute casts for this model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'roles' => 'array',
        ];
    }

    /**
     * Returns the Gravatar URL for this user's email.
     */
    public function getAvatarUrl(): string
    {
        return Gravatar::getAvatarUrl((string) $this->email);
    }
}
