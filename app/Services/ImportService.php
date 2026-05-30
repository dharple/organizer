<?php

/**
 * This file is part of the Organizer package.
 *
 * (c) Doug Harple <dharple@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Services;

use App\Models\Box;
use App\Models\BoxModel;
use App\Models\Location;
use App\Support\Serializer\ExportContainerDenormalizer;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Imports a full export file into the database.
 */
class ImportService
{
    /**
     * Constructor.
     */
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * Imports data from the given file according to options.
     *
     * @param array<string, mixed> $options
     *
     * @throws Exception
     */
    public function import(array $options): void
    {
        $this->logger->info(json_encode($options, JSON_PARTIAL_OUTPUT_ON_ERROR));

        if (Box::count() > 0 || BoxModel::count() > 0 || Location::count() > 0) {
            throw new Exception('Database is already populated.  Cannot import with existing data.');
        }

        if (!isset($options['filename'])) {
            throw new Exception('Missing filename');
        }

        $format = $options['format'] ?? pathinfo((string) $options['filename'], PATHINFO_EXTENSION);

        $encoders = [new JsonEncoder(), new XmlEncoder(), new YamlEncoder()];
        $normalizers = [new ExportContainerDenormalizer()];
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
            if (!($options['dry-run'] ?? false)) {
                $location->save();
            }
        }

        foreach ($container->getBoxModels() as $boxModel) {
            printf("Importing Box Model: %s\n", $boxModel->getDisplayLabel());
            if (!($options['dry-run'] ?? false)) {
                $boxModel->save();
            }
        }

        foreach ($container->getBoxes() as $box) {
            printf("Importing Box: %s\n", $box->getDisplayLabel());
            if (!($options['dry-run'] ?? false)) {
                $box->save();
            }
        }
    }
}
