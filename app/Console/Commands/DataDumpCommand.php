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

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Utility\Separator;

/**
 * Exports multiple version of the current data set to a configured location.
 */
#[Signature('data:dump')]
#[Description('Runs several data exports')]
class DataDumpCommand extends Command
{
    use VerbosityTrait;

    /**
     * Subcommand to run.
     *
     * @var string
     */
    protected const SUB_COMMAND = 'data:export';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->info(Separator::generate(title: date('c')));
        }

        $path = config('custom.dump.path');
        if (!is_dir($path)) {
            mkdir($path, 0o775, true);
        }

        $verbosityFlag = $this->getVerbosityFlag();

        foreach (config('custom.dump.exports') as $row) {
            $options = [
                '--force'  => true,
                '--format' => $row['format'],
                '--type'   => $row['type'],
                '--output' => sprintf('%s/data-%s.%s', $path, $row['type'], $row['format']),
            ];

            if ($verbosityFlag) {
                $options[$verbosityFlag] = true;
            }

            if ($this->getOutput()->isDebug()) {
                $this->newLine();
                $this->line(Separator::generate(title: 'options', align: Separator::ALIGN_RIGHT));
                $this->line(sprintf('calling %s with options %s', static::SUB_COMMAND, var_export($options, true)));
                $this->line(Separator::generate());
                $this->newLine();
            }

            $this->call(static::SUB_COMMAND, $options);
        }
    }
}
