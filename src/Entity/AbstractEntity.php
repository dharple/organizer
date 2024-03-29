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

use DateTime;
use Doctrine\Common\Collections\Collection;
use Exception;
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
        $ret = [
            'displayLabel' => $this->getDisplayLabel(),
        ];

        foreach (get_object_vars($this) as $field => $value) {
            if (str_starts_with($field, '__')) {
                continue;
            }

            if (is_array($value)) {
                continue;
            }

            if (is_object($value)) {
                if ($value instanceof Collection) {
                    continue;
                }

                try {
                    if ($value instanceof EntityInterface) {
                        $value = $value->getId();
                    } elseif ($value instanceof DateTime) {
                        $value = $value->format('c');
                    } else {
                        $value = (string) $value;
                    }
                } catch (Exception) {
                    continue;
                }
            }

            $ret[$field] = $value;
        }

        ksort($ret);

        return $ret;
    }

    /**
     * Generates the display label for this class.
     *
     * @return string
     */
    abstract public function getDisplayLabel();
}
