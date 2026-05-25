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
use Illuminate\Console\Command;

/**
 * Lists all locations with box counts.
 */
class LocationListCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List locations';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:list';

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
