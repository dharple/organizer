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

use App\Services\ImportService;
use Exception;
use Illuminate\Console\Command;

/**
 * Imports box and related data from a file.
 */
class DataImportCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports box and related data';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:import
        {filename       : The file to import from}
        {--dry-run      : Simulate the import without affecting the database}
        {--format=      : Import format: json, xml, yaml (defaults to file extension)}';

    /**
     * Constructor.
     */
    public function __construct(protected ImportService $importService)
    {
        parent::__construct();
    }

    /**
     * Executes the command.
     */
    public function handle(): int
    {
        try {
            $this->importService->import([
                'dry-run'  => $this->option('dry-run'),
                'filename' => $this->argument('filename'),
                'format'   => $this->option('format'),
            ]);
        } catch (Exception $e) {
            $this->error('Failed to import: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->info('File imported.');

        return self::SUCCESS;
    }
}
