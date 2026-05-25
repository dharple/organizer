<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reverses the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }

    /**
     * Runs the migration.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 180)->unique();
            $table->string('password');
            $table->json('roles')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
