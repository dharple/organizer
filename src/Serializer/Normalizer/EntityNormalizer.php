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

use App\Entity\EntityInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Normalizes a entity into its IDs
 */
class EntityNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, SerializerAwareInterface
{
    use SerializerAwareTrait {
        setSerializer as traitSetSerializer;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * Normalizes an entity
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        return $entity->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof EntityInterface;
    }
}
