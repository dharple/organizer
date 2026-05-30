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

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a box model (type/size of container).
 *
 * @property int      $id
 * @property string   $color
 * @property string   $label
 * @property string   $latch
 * @property string   $make
 * @property string   $model
 * @property string   $size
 */
#[Fillable([
    'color',
    'label',
    'latch',
    'make',
    'model',
    'size',
])]
#[Table(name: 'box_model')]
class BoxModel extends BaseModel
{
    /**
     * Whether the model uses created_at and updated_at columns.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Returns the boxes relationship.
     */
    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class, 'box_model_id');
    }

    /**
     * Returns the display label for this box model.
     */
    public function getDisplayLabel(): string
    {
        return (string) $this->label;
    }
}
