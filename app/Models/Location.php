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

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a storage location, which may have a parent location.
 */
class Location extends BaseModel
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hide_from_search' => 'boolean',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'description',
        'hide_from_search',
        'label',
        'parent_location_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * Returns the boxes relationship.
     */
    public function boxes(): HasMany
    {
        return $this->hasMany(Box::class, 'location_id');
    }

    /**
     * Generates the display label for this location, showing its full place on the tree.
     *
     * @throws Exception
     */
    public function getDisplayLabel(): string
    {
        $build = [];
        foreach ($this->parentWalker() as $location) {
            $build[] = $location->label;
        }

        return implode(' - ', array_reverse($build));
    }

    /**
     * Returns the parent location relationship.
     */
    public function parentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_location_id');
    }

    /**
     * Generator that walks back through parents and yields each generation.
     *
     * Throws an exception if the same ID appears twice (circular hierarchy).
     *
     * @throws Exception
     */
    protected function parentWalker(): iterable
    {
        $ids = [];
        $objs = [];

        $location = $this;
        while ($location !== null) {
            if ($location->id !== null) {
                if (in_array($location->id, $ids)) {
                    throw new Exception('Recursive location hierarchy found');
                }
                $ids[] = $location->id;
            } else {
                if (in_array($location, $objs, true)) {
                    throw new Exception('Recursive location hierarchy found');
                }
                $objs[] = $location;
            }

            yield $location;
            $location = $location->parentLocation;
        }
    }

    /**
     * Sets the parent location, validating against circular hierarchies.
     *
     * @throws Exception
     */
    public function setParentLocation(?Location $parentLocation): self
    {
        $holdId = $this->parent_location_id;
        $holdRelation = $this->relationLoaded('parentLocation') ? $this->getRelation('parentLocation') : null;

        $this->parent_location_id = $parentLocation?->id;
        $this->setRelation('parentLocation', $parentLocation);

        try {
            iterator_to_array($this->parentWalker());
        } catch (Exception $e) {
            $this->parent_location_id = $holdId;
            if ($holdRelation !== null) {
                $this->setRelation('parentLocation', $holdRelation);
            } else {
                $this->unsetRelation('parentLocation');
            }
            throw $e;
        }

        return $this;
    }
}
