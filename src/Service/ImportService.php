<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Box;
use App\Entity\BoxModel;
use App\Entity\Location;
use App\Serializer\Normalizer\ExportContainerDenormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Importer
 */
class ImportService
{
    /**
     * Constructs a new Import service
     */
    public function __construct(
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * Imports from a file.
     *
     * @throws Exception
     */
    public function import(array $options)
    {
        $this->logger->info(json_encode($options, JSON_PARTIAL_OUTPUT_ON_ERROR));

        if (
            $this->em->getRepository(Box::class)->count([]) > 0 ||
            $this->em->getRepository(BoxModel::class)->count([]) > 0 ||
            $this->em->getRepository(Location::class)->count([]) > 0
        ) {
            throw new Exception('Database is already populated.  Cannot import with existing data.');
        }

        if (!isset($options['filename'])) {
            throw new Exception('Missing filename');
        }

        if (isset($options['format'])) {
            $format = $options['format'];
        } else {
            $info = pathinfo((string) $options['filename']);
            $format = $info['extension'];
        }

        $encoders = [
            new JsonEncoder(),
            new XmlEncoder(),
            new YamlEncoder(),
        ];

        $normalizers = [
            new ExportContainerDenormalizer(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        $container = $serializer->deserialize(
            file_get_contents($options['filename']),
            ExportContainer::class,
            $format
        );

        if (!($container instanceof ExportContainer)) {
            throw new Exception('Expected an ExportContainer');
        }

        foreach ($container->getLocations() as $location) {
            printf("Importing Location: %s\n", $location->getDisplayLabel());
            if (!$options['dry-run']) {
                $this->em->persist($location);
                $this->em->flush();
            }
        }

        foreach ($container->getBoxModels() as $boxModel) {
            printf("Importing Box Model: %s\n", $boxModel->getDisplayLabel());
            if (!$options['dry-run']) {
                $this->em->persist($boxModel);
                $this->em->flush();
            }
        }

        foreach ($container->getBoxes() as $box) {
            printf("Importing Box: %s\n", $box->getDisplayLabel());
            if (!$options['dry-run']) {
                $this->em->persist($box);
                $this->em->flush();
            }
        }
    }
}
