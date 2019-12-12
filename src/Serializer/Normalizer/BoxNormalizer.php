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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Normalizes a Box entity
 */
class BoxNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface, SerializerAwareInterface
{
    use ArrayFlattenTrait;
    use SerializerAwareTrait {
        setSerializer as traitSetSerializer;
    }

    /**
     * @var ObjectNormalizer
     */
    protected $normalizer;

    /**
     * Field order and translation.
     *
     * @var array
     */
    protected $order = [
        'ID' => ['id', 'displayId'],
        'Label' => 'label',
        'Description' => 'description',
        'Type' => 'boxModel.label',
        'Location' => 'location.displayLabel',
    ];

    /**
     * Constructs a new Box entity normalizer.
     */
    public function __construct(ObjectNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * Normalizes an object
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        $data = $this->flatten($data, '.');

        $ret = [];
        foreach ($this->order as $destination => $source) {
            if (!is_array($source)) {
                $source = [$source];
            }

            foreach ($source as $sourceColumn) {
                if (array_key_exists($sourceColumn, $data)) {
                    $ret[$destination] = $data[$sourceColumn];
                    unset($data[$sourceColumn]);
                    break;
                }
            }

            if (!array_key_exists($destination, $ret)) {
                $ret[$destination] = null;
            }
        }

        foreach ($data as $field => $value) {
            if (!array_key_exists($field, $ret)) {
                $ret[$field] = $value;
            }
        }

        return $ret;
    }

    /**
     * Sets the serializer to use
     */
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->traitSetSerializer($serializer);
        $this->normalizer->setSerializer($serializer);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Box;
    }
}
