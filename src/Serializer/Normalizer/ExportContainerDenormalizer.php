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

use App\Entity\Box;
use App\Entity\BoxModel;
use App\Entity\Location;
use App\Service\ExportContainer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
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
     */
    public function denormalize($data, $type, $format = null, array $context = [])
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
            $exportContainer->addLocation($location);
            $cache['locations'][$row['id']] = $location;

            if (isset($row['createdAt'])) {
                $location->setCreatedAt(new \DateTime($row['createdAt'], new \DateTimeZone('UTC')));
            }

            if (isset($row['updatedAt'])) {
                $location->setUpdatedAt(new \DateTime($row['updatedAt'], new \DateTimeZone('UTC')));
            }

            if (isset($row['deletedAt'])) {
                $location->setDeletedAt(new \DateTime($row['deletedAt'], new \DateTimeZone('UTC')));
            }
        }

        foreach ($data['locations'] as $row) {
            $location = $cache['locations'][$row['id']];

            if (isset($row['parentLocation'])) {
                $locationId = $row['parentLocation'];
                if (array_key_exists($locationId, $cache['locations'])) {
                    $location->setParentLocation($cache['locations'][$locationId]);
                } else {
                    throw new \Exception('Could not find Parent Location for Location ID ' . $row['id']);
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
            $exportContainer->addBoxModel($boxModel);
            $cache['boxModels'][$row['id']] = $boxModel;

            if (isset($row['createdAt'])) {
                $boxModel->setCreatedAt(new \DateTime($row['createdAt'], new \DateTimeZone('UTC')));
            }

            if (isset($row['updatedAt'])) {
                $boxModel->setUpdatedAt(new \DateTime($row['updatedAt'], new \DateTimeZone('UTC')));
            }

            if (isset($row['deletedAt'])) {
                $boxModel->setDeletedAt(new \DateTime($row['deletedAt'], new \DateTimeZone('UTC')));
            }
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
            $exportContainer->addBox($box);
            $cache['box'][$row['id']] = $box;

            if (isset($row['location'])) {
                $locationId = $row['location'];
                if (array_key_exists($locationId, $cache['locations'])) {
                    $box->setLocation($cache['locations'][$locationId]);
                } else {
                    throw new \Exception('Could not find Location for Box ID ' . $row['id']);
                }
            }

            if (isset($row['boxModel'])) {
                $boxModelId = $row['boxModel'];
                if (array_key_exists($boxModelId, $cache['boxModels'])) {
                    $box->setBoxModel($cache['boxModels'][$boxModelId]);
                } else {
                    throw new \Exception('Could not find Box Model for Box ID ' . $row['id']);
                }
            }

            if (isset($row['createdAt'])) {
                $box->setCreatedAt(new \DateTime($row['createdAt'], new \DateTimeZone('UTC')));
            }

            if (isset($row['updatedAt'])) {
                $box->setUpdatedAt(new \DateTime($row['updatedAt'], new \DateTimeZone('UTC')));
            }

            if (isset($row['deletedAt'])) {
                $box->setDeletedAt(new \DateTime($row['deletedAt'], new \DateTimeZone('UTC')));
            }
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
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return ($type === 'App\Service\ExportContainer');
    }
}
