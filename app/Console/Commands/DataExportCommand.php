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

use App\Services\ExportService;
use Exception;
use Illuminate\Console\Command;

/**
 * Exports box and related data to a file.
 */
class DataExportCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exports box and related data';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:export
        {--force       : Force the file to write out, even if it already exists}
        {--format=json : Export format: csv, json, ods, xml, xlsx, yaml}
        {--output=     : Output filename (defaults to export-YYYYMMDDHHMM.{format})}
        {--type=full   : Export type: full, simple}';

    /**
     * Constructor.
     */
    public function __construct(protected ExportService $exportService)
    {
        parent::__construct();
    }

    /**
     * Executes the command.
     */
    public function handle(): int
    {
        try {
            $format = $this->option('format') ?? 'json';
            $type   = $this->option('type') ?? 'full';

            $file = $this->exportService->export([
                'format' => $format,
                'type'   => $type,
            ]);

            $output = $this->option('output') ?? $file->getSuggestedFilename();

            if (file_exists($output)) {
                if (!$this->option('force')) {
                    $this->error(sprintf('File %s already exists.  Pass --force to overwrite it.', $output));
                    return 2;
                }

                $this->warn(sprintf('Overwriting %s.', $output));
            }

            rename($file->getFilename(), $output);

            $this->info(sprintf('Wrote %s export to %s', $format, $output));
        } catch (Exception $e) {
            $this->error('Failed to export: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
