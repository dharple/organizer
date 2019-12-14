<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utility;

use App\Entity\EntityInterface;

/**
 * Methods related to sorting
 */
class Sort
{
    /**
     * Prevent instantiation
     */
    private function __construct()
    {
    }

    /**
     * Method for sorting locations by the method getDisplayLabel
     *
     * @return int
     */
    public static function sortByDisplayLabel(EntityInterface $a, EntityInterface $b): int
    {
        $a = $a->getDisplayLabel();
        $b = $b->getDisplayLabel();
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
}
