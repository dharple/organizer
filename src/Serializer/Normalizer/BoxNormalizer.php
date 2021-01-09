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
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Normalizes a Box entity
 */
class BoxNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, SerializerAwareInterface
{
    use SerializerAwareTrait {
        setSerializer as traitSetSerializer;
    }

    /**
     *
     */
    protected $useDisplayId = false;

    /**
     * Constructs a new Box entity normalizer.
     */
    public function __construct(bool $useDisplayId = null)
    {
        if (!is_null($useDisplayId)) {
            $this->useDisplayId = $useDisplayId;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * Normalizes a box
     */
    public function normalize($box, $format = null, array $context = []): array
    {
        return [
            'Box Number' => $this->useDisplayId ? $box->getDisplayId() : $box->getBoxNumber(),
            'Label' => $box->getLabel(),
            'Description' => $box->getDescription(),
            'Type' => $box->getBoxModel() ? $box->getBoxModel()->getDisplayLabel() : null,
            'Location' => $box->getLocation() ? $box->getLocation()->getDisplayLabel() : null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Box;
    }
}
