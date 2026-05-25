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

use App\Models\Box;
use Illuminate\Console\Command;

/**
 * Lists boxes with optional filtering.
 */
class BoxListCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List boxes';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'box:list
        {--box=* : Box Number(s) to search on}
        {--id=*  : Box ID(s) to search on}
        {--location=* : Location ID(s) to search on}';

    /**
     * Executes the command.
     */
    public function handle(): int
    {
        $query = Box::query();

        $boxNumbers = $this->option('box');
        if (!empty($boxNumbers)) {
            $query->whereIn('box_number', $boxNumbers);
        }

        $ids = $this->option('id');
        if (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $locations = $this->option('location');
        if (!empty($locations)) {
            $query->whereIn('location_id', $locations);
        }

        $boxes = $query->orderBy('box_number')->get();

        $this->table(
            ['id', 'label', 'location'],
            $boxes->map(fn(Box $box) => [
                $box->id,
                $box->getDisplayLabel(),
                $box->location ? $box->location->getDisplayLabel() : '~',
            ])
        );

        return self::SUCCESS;
    }
}
