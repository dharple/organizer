<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Support;

use App\Models\ModelInterface;

/**
 * Utility sorting functions for Organizer models.
 */
class Sort
{
    /**
     * Not instantiable.
     */
    private function __construct()
    {
    }

    /**
     * Compares two models by their display label for use with usort().
     */
    public static function sortByDisplayLabel(ModelInterface $a, ModelInterface $b): int
    {
        return $a->getDisplayLabel() <=> $b->getDisplayLabel();
    }
}
