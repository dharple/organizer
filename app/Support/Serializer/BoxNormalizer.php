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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Normalizes a Box model for serialization.
 */
class BoxNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * Whether to use the display ID (zero-padded) instead of the raw box number.
     *
     * @var bool
     */
    protected bool $useDisplayId = false;

    /**
     * Constructor.
     */
    public function __construct(?bool $useDisplayId = null)
    {
        if ($useDisplayId !== null) {
            $this->useDisplayId = $useDisplayId;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedTypes(?string $format): array
    {
        return [Box::class => true];
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(mixed $box, ?string $format = null, array $context = []): array
    {
        return [
            'Box Number'  => $this->useDisplayId ? $box->getDisplayId() : $box->box_number,
            'Label'       => $box->label,
            'Description' => $box->description,
            'Type'        => $box->boxModel ? $box->boxModel->getDisplayLabel() : null,
            'Location'    => $box->location ? $box->location->getDisplayLabel() : null,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Box;
    }
}
