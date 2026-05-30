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

/**
 * Helps carry the verbosity settings from the command line down to a
 * subcommand.
 */
trait VerbosityTrait
{
    /**
     * Returns a verbosity flag equivalent to the current verbosity level, for
     * the purpose of sending that verbosity flag to a subcommand.
     */
    protected function getVerbosityFlag(): ?string
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
