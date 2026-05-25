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

use App\Services\MoveService;
use Exception;
use Illuminate\Console\Command;

/**
 * Moves boxes between locations.
 */
class BoxMoveCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move boxes';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'box:move
        {--box=*     : Box Number(s) to move}
        {--dry-run   : Simulate the move without affecting the database}
        {--from=*    : Source Location ID(s)}
        {--id=*      : Box ID(s) to move}
        {--to=       : Destination Location ID}';

    /**
     * Constructor.
     */
    public function __construct(protected MoveService $moveService)
    {
        parent::__construct();
    }

    /**
     * Executes the command.
     */
    public function handle(): int
    {
        try {
            $results = $this->moveService->move([
                'box'      => $this->option('box'),
                'dry-run'  => $this->option('dry-run'),
                'from'     => $this->option('from'),
                'id'       => $this->option('id'),
                'to'       => $this->option('to'),
            ]);

            $this->table(['id', 'label', 'from', 'to'], $results);

            if ($this->option('dry-run')) {
                $this->warn('Running in dry run mode.');
            } else {
                $this->info('Items moved.');
            }
        } catch (Exception $e) {
            $this->error('Failed to move: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
