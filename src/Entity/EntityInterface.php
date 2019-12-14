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
 * Defines a class that supports getData()
 */
interface EntityInterface
{
    /**
     * Returns an array containing the raw data for this entity
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Generates the display label for this class.
     *
     * @return string
     */
    public function getDisplayLabel();

    /**
     * Returns the ID of this entity.
     */
    public function getId(): ?int;
}
