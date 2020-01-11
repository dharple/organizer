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

use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Base entity methods
 */
abstract class AbstractEntity
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * Returns an array containing the raw data for this entity
     *
     * @return array
     */
    public function getData(): array
    {
        $ret = [];

        foreach (get_object_vars($this) as $field => $value) {
            if (strpos($field, '__') === 0) {
                continue;
            }

            if (is_array($value)) {
                continue;
            }

            if (is_object($value)) {
                try {
                    if ($value instanceof EntityInterface) {
                        $value = $value->getId();
                    } elseif ($value instanceof \DateTime) {
                        $value = $value->format('c');
                    } else {
                        $value = (string) $value;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            $ret[$field] = $value;
        }

        return $ret;
    }
}
