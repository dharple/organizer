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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents a storage box.
 *
 * @property int      $id
 * @property Carbon   $created_at
 * @property ?Carbon  $updated_at
 * @property ?int     $box_model_id
 * @property int      $box_number
 * @property string   $description
 * @property string   $label
 * @property ?int     $location_id
 */
class Box extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'box_model_id',
        'box_number',
        'description',
        'label',
        'location_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'box';

    /**
     * Registers model events.
     */
    protected static function booted(): void
    {
        static::creating(function (Box $box) {
            if (empty($box->box_number)) {
                $box->box_number = (Box::withTrashed()->max('box_number') ?? 0) + 1;
            }
        });
    }

    /**
     * Returns the box model relationship.
     */
    public function boxModel(): BelongsTo
    {
        return $this->belongsTo(BoxModel::class, 'box_model_id');
    }

    /**
     * Returns the zero-padded box number string.
     */
    public function getDisplayId(): string
    {
        return empty($this->box_number) ? '' : sprintf('%04d', $this->box_number);
    }

    /**
     * Returns the display label for this box.
     */
    public function getDisplayLabel(): string
    {
        return empty($this->box_number)
        ? (string) $this->label
        : sprintf('Box %04d - %s', $this->box_number, $this->label);
    }

    /**
     * Returns true if this box's location is hidden from search.
     */
    public function isHidden(): bool
    {
        $location = $this->location;
        if ($location === null) {
            return false;
        }

        return (bool) $location->hide_from_search;
    }

    /**
     * Returns the location relationship.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Scopes the query to boxes updated within the given time window.
     */
    public function scopeRecent(Builder $query, string $since = '-30 days', ?int $limit = null): Builder
    {
        $q = $query
            ->where('updated_at', '>=', Carbon::parse($since))
            ->orderByDesc('updated_at');

        if ($limit !== null) {
            $q->limit($limit);
        }

        return $q;
    }

    /**
     * Scopes the query to boxes sorted by box number.
     */
    public function scopeSortedByDisplayLabel(Builder $query): Builder
    {
        return $query->orderBy('box_number');
    }
}
