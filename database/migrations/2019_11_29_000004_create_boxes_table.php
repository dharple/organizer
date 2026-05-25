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
        Schema::dropIfExists('boxes');
    }

    /**
     * Runs the migration.
     */
    public function up(): void
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->integer('box_number')->unique();
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('box_model_id')->nullable()->constrained('box_models')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
