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
use App\Serializer\Normalizer\BoxNormalizer;
use App\Serializer\Normalizer\EntityNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Exporter
 */
class ExportService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructs a new Export service
     */
    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * Builds an export
     */
    public function export(array $options): ExportResponse
    {
        $this->logger->info(json_encode($options));

        if ($options['type'] == 'simple') {
            switch ($options['format']) {
                case 'json':
                case 'xml':
                    return $this->simpleUseSerializer($options);

                case 'csv':
                case 'ods':
                case 'xlsx':
                    return $this->simpleUsePhpSpreadsheet($options);

                default:
                    throw new \Exception('Invalid format: ' . $options['format']);
            }
        } else {
            switch ($options['format']) {
                case 'json':
                case 'xml':
                    return $this->fullUseSerializer($options);

                case 'csv':
                case 'ods':
                case 'xlsx':
                    throw new \Exception('Format "' . $options['format'] . '" does not support full exports');

                default:
                    throw new \Exception('Invalid format: ' . $options['format']);
            }
        }
    }

    /**
     * Uses the Symfony serializer to export data
     */
    protected function fullUseSerializer(array $options): ExportResponse
    {
        $data = [
            'boxes' => $this->em->getRepository(Box::class)->getSorted(),
            'boxModels' => $this->em->getRepository(BoxModel::class)->getSorted(),
            'locations' => $this->em->getRepository(Location::class)->getSorted(),
        ];

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new EntityNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $data = $serializer->serialize(
            $data,
            $options['format']
        );

        $this->logger->info($data);

        $response = (new ExportResponse())
            ->setFormat($options['format'])
            ->setData($data);

        return $response;
    }

    /**
     * Uses PHPSpreadsheet to export data
     */
    protected function simpleUsePhpSpreadsheet(array $options): ExportResponse
    {
        $boxes = $this->em->getRepository(Box::class)->getSorted();

        $normalizers = [new BoxNormalizer(false)];
        $serializer = new Serializer($normalizers);

        $data = $serializer->normalize(
            $boxes,
            null,
            [
                AbstractNormalizer::ATTRIBUTES => [
                    'id',
                    'label',
                    'description',
                    'boxModel' => ['label'],
                    'location' => ['displayLabel'],
                ],
            ]
        );

        if (empty($data)) {
            throw new \Exception('No data to export');
        }
        $headerRow = array_keys($data[array_key_first($data)]);

        $this->logger->info(var_export($data, true));

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCompany('Organizer')
            ->setCreator('Organizer')
            ->setDescription('Exported box information')
            ->setLastModifiedBy('Organizer')
            ->setSubject('Boxes')
            ->setTitle('Organizer Export');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(array_merge([$headerRow], $data));

        $column = $sheet->getColumnIterator('A')->current();
        foreach ($column->getCellIterator(2) as $cell) {
            $cell->getStyle()->getNumberFormat()->setFormatCode('000#');
        }

        $column = $sheet->getColumnIterator('C')->current();
        foreach ($column->getCellIterator(2) as $cell) {
            $cell->getStyle()->getAlignment()->setWrapText(true);
        }

        foreach ($headerRow as $column => $label) {
            $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }

        $filename = tempnam('/tmp', 'export_spreadsheet_');
        switch ($options['format']) {
            case 'csv':
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
                break;

            case 'ods':
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Ods($spreadsheet);
                break;

            case 'xlsx':
                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                break;

            default:
                throw new \Exception('Invalid format: ' . $options['format']);
        }

        $writer->save($filename);

        $response = (new ExportResponse())
            ->setFormat($options['format'])
            ->setFilename($filename);

        return $response;
    }

    /**
     * Uses the Symfony serializer to export data
     */
    protected function simpleUseSerializer(array $options): ExportResponse
    {
        $boxes = $this->em->getRepository(Box::class)->getSorted();

        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder()];
        $normalizers = [new BoxNormalizer(true)];
        $serializer = new Serializer($normalizers, $encoders);

        $data = $serializer->serialize(
            $boxes,
            $options['format'],
            [
                AbstractNormalizer::ATTRIBUTES => [
                    'displayId',
                    'label',
                    'description',
                    'boxModel' => ['label'],
                    'location' => ['displayLabel'],
                ],
            ]
        );

        $this->logger->info($data);

        $response = (new ExportResponse())
            ->setFormat($options['format'])
            ->setData($data);

        return $response;
    }
}