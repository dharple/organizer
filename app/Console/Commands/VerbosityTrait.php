<?php

namespace App\Console\Commands;

trait VerbosityTrait
{
    protected function getVerbosityFlag()
    {
        if ($this->getOutput()->isDebug()) {
            return '-vvv';
        } elseif ($this->getOutput()->isVeryVerbose()) {
            return '-vv';
        } elseif ($this->getOutput()->isVerbose()) {
            return '-v';
        }

        return null;
    }
}
