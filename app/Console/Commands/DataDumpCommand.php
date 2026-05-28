<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Utility\Separator;

#[Signature('data:dump')]
#[Description('Runs several data exports')]
class DataDumpCommand extends Command
{
    use VerbosityTrait;

    /**
     *
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
