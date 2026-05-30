<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services;

use App\Models\Box;
use App\Models\Location;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Moves boxes between locations.
 */
class MoveService
{
    /**
     * Constructor.
     */
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * Moves boxes according to the given options and returns a summary.
     *
     * @param array<string, mixed> $options
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws Exception
     */
    public function move(array $options): array
    {
        $this->logger->info(json_encode($options, JSON_PARTIAL_OUTPUT_ON_ERROR));

        if (empty($options['box']) && empty($options['id']) && empty($options['from'])) {
            throw new Exception('At least one Box ID, Box Number, or Source Location must be specified');
        }

        if (!isset($options['to'])) {
            throw new Exception('A Destination Location must be specified');
        }

        $toLocation = Location::find($options['to']);
        if (!$toLocation) {
            throw new Exception('Destination Location not found: ' . $options['to']);
        }

        $query = Box::query();

        if (!empty($options['box'])) {
            $query->whereIn('box_number', (array) $options['box']);
        }
        if (!empty($options['id'])) {
            $query->whereIn('id', (array) $options['id']);
        }
        if (!empty($options['from'])) {
            $query->whereIn('location_id', (array) $options['from']);
        }

        $boxes = $query->orderBy('box_number')->get();

        $ret = [];
        foreach ($boxes as $box) {
            $ret[] = [
                'id'    => $box->id,
                'label' => $box->getDisplayLabel(),
                'from'  => $box->location ? $box->location->getDisplayLabel() : '~',
                'to'    => $toLocation->getDisplayLabel(),
            ];

            if (!($options['dry-run'] ?? false)) {
                $box->location_id = $toLocation->id;
                $box->save();
            }
        }

        return $ret;
    }
}
