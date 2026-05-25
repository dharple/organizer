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

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a box model (type/size of container).
 */
class BoxModel extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'color',
        'label',
        'latch',
        'make',
        'model',
        'size',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'box_models';

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
