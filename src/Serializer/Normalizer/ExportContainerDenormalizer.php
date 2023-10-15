<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Serializer\Normalizer;

use App\Entity\AbstractEntity;
use App\Entity\Box;
use App\Entity\BoxModel;
use App\Entity\Location;
use App\Service\ExportContainer;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Turns a parsed export file into entities
 */
class ExportContainerDenormalizer implements CacheableSupportsMethodInterface, DenormalizerInterface
{
    /**
     * @var AbstractObjectNormalizer
     */
    protected $normalizer;

    /**
     * Construct a new denormalizer
     */
    public function __construct(?AbstractObjectNormalizer $normalizer = null)
    {
        $this->normalizer = $normalizer ?? new ObjectNormalizer();
    }

    /**
     * Denormalizes an entity
     *
     * @throws Exception
     */
    public function denormalize($data, $type, $format = null, array $context = []): mixed
    {
        $exportContainer = new ExportContainer();

        $cache = [
            'boxes'     => [],
            'boxModels' => [],
            'locations' => [],
        ];

        foreach ($data['locations'] as $row) {
            $location = $this->normalizer->denormalize(
                $row,
                Location::class,
                $format,
                [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => [
                        'parentLocation',
                        'createdAt',
                        'updatedAt',
                        'deletedAt',
                    ],
                ]
            );

            if (!($location instanceof Location)) {
                throw new Exception('Expected a Location');
            }

            $exportContainer->addLocation($location);
            $cache['locations'][$row['id']] = $location;

            $this->normalizeTimestamps($location, $row);
        }

        foreach ($data['locations'] as $row) {
            $location = $cache['locations'][$row['id']];

            if (isset($row['parentLocation'])) {
                $locationId = $row['parentLocation'];
                if (array_key_exists($locationId, $cache['locations'])) {
                    $location->setParentLocation($cache['locations'][$locationId]);
                } else {
                    throw new Exception('Could not find Parent Location for Location ID ' . $row['id']);
                }
            }
        }

        foreach ($data['boxModels'] as $row) {
            $boxModel = $this->normalizer->denormalize(
                $row,
                BoxModel::class,
                $format,
                [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => [
                        'createdAt',
                        'updatedAt',
                        'deletedAt',
                    ],
                ]
            );

            if (!($boxModel instanceof BoxModel)) {
                throw new Exception('Expected a BoxModel');
            }

            $exportContainer->addBoxModel($boxModel);
            $cache['boxModels'][$row['id']] = $boxModel;

            $this->normalizeTimestamps($boxModel, $row);
        }

        foreach ($data['boxes'] as $row) {
            $box = $this->normalizer->denormalize(
                $row,
                Box::class,
                $format,
                [
                    AbstractNormalizer::IGNORED_ATTRIBUTES => [
                        'location',
                        'boxModel',
                        'createdAt',
                        'updatedAt',
                        'deletedAt',
                    ],
                ]
            );

            if (!($box instanceof Box)) {
                throw new Exception('Expected a Box');
            }

            $exportContainer->addBox($box);
            $cache['box'][$row['id']] = $box;

            if (isset($row['location'])) {
                $locationId = $row['location'];
                if (array_key_exists($locationId, $cache['locations'])) {
                    $box->setLocation($cache['locations'][$locationId]);
                } else {
                    throw new Exception('Could not find Location for Box ID ' . $row['id']);
                }
            }

            if (isset($row['boxModel'])) {
                $boxModelId = $row['boxModel'];
                if (array_key_exists($boxModelId, $cache['boxModels'])) {
                    $box->setBoxModel($cache['boxModels'][$boxModelId]);
                } else {
                    throw new Exception('Could not find Box Model for Box ID ' . $row['id']);
                }
            }

            $this->normalizeTimestamps($box, $row);
        }

        return $exportContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * Normalize timestamps
     *
     * @param AbstractEntity $entity
     * @param array          $row
     *
     * @throws Exception
     */
    protected function normalizeTimestamps($entity, $row): void
    {
        if (isset($row['createdAt'])) {
            $entity->setCreatedAt(new DateTime($row['createdAt'], new DateTimeZone('UTC')));
        }

        if (isset($row['updatedAt'])) {
            $entity->setUpdatedAt(new DateTime($row['updatedAt'], new DateTimeZone('UTC')));
        }

        if (isset($row['deletedAt'])) {
            $entity->setDeletedAt(new DateTime($row['deletedAt'], new DateTimeZone('UTC')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === \App\Service\ExportContainer::class);
    }
}
