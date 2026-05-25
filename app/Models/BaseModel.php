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

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Base Eloquent model for Organizer.
 */
abstract class BaseModel extends Model implements ModelInterface
{
    use SoftDeletes;

    /**
     * Returns all data for this model as a flat array.
     */
    public function getData(): array
    {
        $ret = [
            'display_label' => $this->getDisplayLabel(),
        ];

        foreach ($this->getAttributes() as $field => $value) {
            if ($value instanceof Carbon) {
                $value = $value->format('c');
            }
            $ret[$field] = $value;
        }

        ksort($ret);

        return $ret;
    }

    /**
     * Returns the display label for this model.
     */
    abstract public function getDisplayLabel(): string;
}
