<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Support\Serializer;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use App\Services\ExportContainer;
use Carbon\Carbon;
use Exception;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Denormalizes a full export array back into an ExportContainer.
 */
class ExportContainerDenormalizer implements DenormalizerInterface
{
    /**
     * Applies created_at, updated_at, and deleted_at from an import row to a model.
     *
     * @param object               $model The object to update.
     * @param array<string, mixed> $row   The row to pull timestamps from.
     */
    protected function applyTimestamps(object $model, array $row): void
    {
        $model->timestamps = false;

        $createdAt = $this->getValue($row, 'createdAt', 'created_at');
        if ($createdAt !== null) {
            $model->created_at = Carbon::parse($createdAt);
        }

        $updatedAt = $this->getValue($row, 'updatedAt', 'updated_at');
        if ($updatedAt !== null) {
            $model->updated_at = Carbon::parse($updatedAt);
        }

        $deletedAt = $this->getValue($row, 'deletedAt', 'deleted_at');
        if ($deletedAt !== null) {
            $model->deleted_at = Carbon::parse($deletedAt);
        }
    }

    /**
     * {@inhertDoc}
     *
     * @throws Exception
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ExportContainer
    {
        $exportContainer = new ExportContainer();

        $locationCache = [];
        $boxModelCache = [];

        foreach ($data['locations'] ?? [] as $row) {
            $location = new Location();
            $location->forceFill([
                'id'               => $row['id'] ?? null,
                'label'            => $row['label'],
                'description'      => $row['description'] ?? null,
                'hide_from_search' => (bool) ($this->getValue($row, 'hideFromSearch', 'hide_from_search') ?? false),
            ]);

            $this->applyTimestamps($location, $row);

            $exportContainer->addLocation($location);
            $locationCache[$row['id']] = $location;
        }

        foreach ($data['locations'] ?? [] as $row) {
            $parentId = $this->getValue($row, 'parentLocation', 'parent_location_id');
            if ($parentId === null) {
                continue;
            }
            if (!array_key_exists($parentId, $locationCache)) {
                throw new Exception('Could not find Parent Location for Location ID ' . $row['id']);
            }
            $locationCache[$row['id']]->setParentLocation($locationCache[$parentId]);
        }

        foreach ($this->getValue($data, 'boxModels', 'box_models') ?? [] as $row) {
            $boxModel = new BoxModel();
            $boxModel->forceFill([
                'id'    => $row['id'] ?? null,
                'color' => $row['color'] ?? null,
                'label' => $row['label'],
                'latch' => $row['latch'] ?? null,
                'make'  => $row['make'] ?? null,
                'model' => $row['model'] ?? null,
                'size'  => $row['size'] ?? null,
            ]);

            $this->applyTimestamps($boxModel, $row);

            $exportContainer->addBoxModel($boxModel);
            $boxModelCache[$row['id']] = $boxModel;
        }

        foreach ($data['boxes'] ?? [] as $row) {
            $box = new Box();
            $box->forceFill([
                'id'          => $row['id'] ?? null,
                'box_number'  => $this->getValue($row, 'boxNumber', 'box_number'),
                'description' => $row['description'] ?? null,
                'label'       => $row['label'],
            ]);

            $locationId = $this->getValue($row, 'location', 'location_id');
            if ($locationId !== null) {
                if (!array_key_exists($locationId, $locationCache)) {
                    throw new Exception('Could not find Location for Box ID ' . $row['id']);
                }
                $box->location_id = $locationId;
                $box->setRelation('location', $locationCache[$locationId]);
            }

            $boxModelId = $this->getValue($row, 'boxModel', 'box_model_id');
            if ($boxModelId !== null) {
                if (!array_key_exists($boxModelId, $boxModelCache)) {
                    throw new Exception('Could not find Box Model for Box ID ' . $row['id']);
                }
                $box->box_model_id = $boxModelId;
                $box->setRelation('boxModel', $boxModelCache[$boxModelId]);
            }

            $this->applyTimestamps($box, $row);

            $exportContainer->addBox($box);
        }

        return $exportContainer;
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedTypes(?string $format): array
    {
        return [ExportContainer::class => true];
    }

    /**
     * Returns a value from an import row, preferring the camelCase key used
     * by current exports over the snake_case key used by older exports.
     *
     * Empty strings are treated as missing, since XML exports encode null
     * values that way.
     *
     * @param array<string, mixed> $row      The row to pull the value from.
     * @param string               $camelKey The camelCase key.
     * @param string               $snakeKey The snake_case key.
     */
    protected function getValue(array $row, string $camelKey, string $snakeKey): mixed
    {
        foreach ([$camelKey, $snakeKey] as $key) {
            if (isset($row[$key]) && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === ExportContainer::class;
    }
}
