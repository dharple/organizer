<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Models;

/**
 * Interface for Organizer models.
 */
interface ModelInterface
{
    /**
     * Returns all data for this model as an array.
     */
    public function getData(): array;

    /**
     * Returns the display label for this model.
     */
    public function getDisplayLabel(): string;
}
