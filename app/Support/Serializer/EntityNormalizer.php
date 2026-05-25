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

use App\Models\ModelInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes any ModelInterface instance using its getData() method.
 */
class EntityNormalizer implements NormalizerInterface
{
    /**
     * Returns the types supported by this normalizer.
     *
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [ModelInterface::class => true];
    }

    /**
     * Normalizes a model entity to an array via getData().
     *
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    public function normalize(mixed $entity, ?string $format = null, array $context = []): array
    {
        return $entity->getData();
    }

    /**
     * Returns true if the data is a ModelInterface instance.
     *
     * @param array<string, mixed> $context
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof ModelInterface;
    }
}
