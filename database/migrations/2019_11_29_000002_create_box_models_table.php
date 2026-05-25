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
        Schema::dropIfExists('box_models');
    }

    /**
     * Runs the migration.
     */
    public function up(): void
    {
        Schema::create('box_models', function (Blueprint $table) {
            $table->id();
            $table->string('label', 255);
            $table->string('make', 64)->nullable();
            $table->string('model', 64)->nullable();
            $table->string('size', 64)->nullable();
            $table->string('color', 64)->nullable();
            $table->string('latch', 16)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
