<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copies data from the singular tables created by the Symfony version of
 * Organizer into the plural tables used by the Laravel version.
 *
 * A table is copied only when both versions exist, the singular table has
 * rows, and the plural table is empty.  The singular tables are left as-is.
 */
return new class extends Migration
{
    /**
     * Plural tables, mapped to the plural tables their foreign keys reference.
     *
     * @var array<string, list<string>>
     */
    private const array FOREIGN_KEYS = [
        'boxes' => ['box_models', 'locations'],
    ];

    /**
     * Plural tables, mapped to their self-referencing foreign key column.
     *
     * @var array<string, string>
     */
    private const array SELF_REFERENCES = [
        'locations' => 'parent_location_id',
    ];

    /**
     * Singular tables, mapped to their plural tables, in foreign key order.
     *
     * @var array<string, string>
     */
    private const array TABLES = [
        'user'      => 'users',
        'box_model' => 'box_models',
        'location'  => 'locations',
        'box'       => 'boxes',
    ];

    /**
     * Copies one singular table into its plural table, preserving ids.
     */
    private function copyTable(string $singular, string $plural): void
    {
        $columns = array_values(array_intersect(
            Schema::getColumnListing($plural),
            Schema::getColumnListing($singular),
        ));

        // A parent may have a higher id than its child, so self-references
        // are restored after every row has been copied.
        $selfReference = self::SELF_REFERENCES[$plural] ?? null;

        $select = array_map(
            fn(string $column) => $column === $selfReference ? DB::raw('NULL') : $column,
            $columns,
        );

        DB::table($plural)->insertUsing($columns, DB::table($singular)->select($select));

        if ($selfReference === null) {
            return;
        }

        $references = DB::table($singular)
            ->whereNotNull($selfReference)
            ->pluck($selfReference, 'id');

        foreach ($references as $id => $referenceId) {
            DB::table($plural)
                ->where('id', $id)
                ->update([$selfReference => $referenceId]);
        }
    }

    /**
     * Reverses the migration.
     */
    public function down(): void
    {
        // The copied rows are left in place, since they may have been
        // modified after the migration ran.
    }

    /**
     * Returns whether a singular table should be copied into its plural table.
     */
    private function shouldCopy(string $singular, string $plural): bool
    {
        if (!Schema::hasTable($singular) || !Schema::hasTable($plural)) {
            return false;
        }

        return DB::table($singular)->exists() && !DB::table($plural)->exists();
    }

    /**
     * Runs the migration.
     *
     * @throws RuntimeException
     */
    public function up(): void
    {
        $copy = [];
        foreach (self::TABLES as $singular => $plural) {
            $copy[$plural] = $this->shouldCopy($singular, $plural);
        }

        // Copied foreign keys would point at the wrong rows if a referenced
        // table already held data of its own.
        foreach (self::FOREIGN_KEYS as $plural => $referencedTables) {
            if (!$copy[$plural]) {
                continue;
            }

            foreach ($referencedTables as $referencedTable) {
                if (!$copy[$referencedTable] && DB::table($referencedTable)->exists()) {
                    throw new RuntimeException(sprintf(
                        'Unable to copy data into %s; %s already contains data.',
                        $plural,
                        $referencedTable,
                    ));
                }
            }
        }

        DB::transaction(function () use ($copy) {
            foreach (self::TABLES as $singular => $plural) {
                if ($copy[$plural]) {
                    $this->copyTable($singular, $plural);
                }
            }
        });
    }
};
