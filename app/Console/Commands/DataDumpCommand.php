<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('data:dump')]
#[Description('Runs several data exports')]
class DataDumpCommand extends Command
{
    /**
     *
     */
    protected const SUB_COMMAND = 'data:export';

    /**
     *
     */
    protected function generateSeparator($title = null, $width = 78)
    {
        if (empty($title)) {
            return str_repeat('-', $width);
        }

        $title = sprintf('[ %s ]', $title);
        $titleLength = strlen($title);

        if ($titleLength < $width) {
            $lineLengthLeft = ceil(($width - $titleLength) / 2);
            $lineLengthRight = $width - $titleLength - $lineLengthLeft;

            return sprintf(
                "%s%s%s",
                str_repeat('-', $lineLengthLeft),
                $title,
                str_repeat('-', $lineLengthRight)
            );
        }

        return $title;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->getOutput()->isVerbose()) {
            $this->info($this->generateSeparator(date('c')));
        }

        $path = config('custom.dump.path');
        if (!is_dir($path)) {
            mkdir($path, 0o775, true);
        }

        $verbosityFlag = null;
        if ($this->getOutput()->isDebug()) {
            $verbosityFlag = '-vvv';
        } elseif ($this->getOutput()->isVeryVerbose()) {
            $verbosityFlag = '-vv';
        } elseif ($this->getOutput()->isVerbose()) {
            $verbosityFlag = '-v';
        }

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
                $this->line($this->generateSeparator('options'));
                $this->line(sprintf('calling %s with options %s', static::SUB_COMMAND, var_export($options, true)));
                $this->line($this->generateSeparator());
                $this->newLine();
            }

            $this->call(static::SUB_COMMAND, $options);
        }
    }
}
