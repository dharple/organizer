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
     * @param array<string, mixed> $row
     */
    protected function applyTimestamps(object $model, array $row): void
    {
        $model->timestamps = false;

        if (isset($row['created_at'])) {
            $model->created_at = Carbon::parse($row['created_at']);
        }

        if (isset($row['updated_at'])) {
            $model->updated_at = Carbon::parse($row['updated_at']);
        }

        if (isset($row['deleted_at'])) {
            $model->deleted_at = Carbon::parse($row['deleted_at']);
        }
    }

    /**
     * Denormalizes an export array into an ExportContainer.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $context
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
                'hide_from_search' => $row['hide_from_search'] ?? false,
            ]);

            $this->applyTimestamps($location, $row);

            $exportContainer->addLocation($location);
            $locationCache[$row['id']] = $location;
        }

        foreach ($data['locations'] ?? [] as $row) {
            if (!isset($row['parent_location_id'])) {
                continue;
            }
            $parentId = $row['parent_location_id'];
            if (!array_key_exists($parentId, $locationCache)) {
                throw new Exception('Could not find Parent Location for Location ID ' . $row['id']);
            }
            $locationCache[$row['id']]->setParentLocation($locationCache[$parentId]);
        }

        foreach ($data['box_models'] ?? [] as $row) {
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
                'box_number'  => $row['box_number'] ?? null,
                'description' => $row['description'] ?? null,
                'label'       => $row['label'],
            ]);

            if (isset($row['location_id'])) {
                $locationId = $row['location_id'];
                if (!array_key_exists($locationId, $locationCache)) {
                    throw new Exception('Could not find Location for Box ID ' . $row['id']);
                }
                $box->location_id = $locationId;
                $box->setRelation('location', $locationCache[$locationId]);
            }

            if (isset($row['box_model_id'])) {
                $boxModelId = $row['box_model_id'];
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
     * Returns the types supported by this denormalizer.
     *
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [ExportContainer::class => true];
    }

    /**
     * Returns true if this denormalizer supports the given type.
     *
     * @param array<string, mixed> $context
     */
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === ExportContainer::class;
    }
}
