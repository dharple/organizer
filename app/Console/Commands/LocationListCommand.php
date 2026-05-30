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

use App\Models\Location;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Lists all locations with box counts.
 */
#[Description('List locations')]
#[Signature('location:list')]
class LocationListCommand extends Command
{
    /**
     * Executes the command.
     */
    public function handle(): int
    {
        $locations = Location::all()
            ->sortBy(fn(Location $l) => $l->getDisplayLabel())
            ->values();

        $this->table(
            ['id', 'label', 'boxes'],
            $locations->map(fn(Location $location) => [
                $location->id,
                $location->getDisplayLabel(),
                $location->boxes()->count(),
            ])
        );

        return self::SUCCESS;
    }
}
