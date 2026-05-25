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
        Schema::dropIfExists('locations');
    }

    /**
     * Runs the migration.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->boolean('hide_from_search')->default(false);
            $table->foreignId('parent_location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
