<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

/**
 * Defines a class that supports getDisplayLabel
 */
interface DisplayableInterface
{
    /**
     * Generates the display label for this class.
     *
     * @return string
     */
    public function getDisplayLabel();
}
