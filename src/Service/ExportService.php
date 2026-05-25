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
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;
use PhpOffice\PhpSpreadsheet\Writer\Ods as OdsWriter;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Exporter
 */
class ExportService
{
    /**
     * Context to pass to the encoder.
     *
     * @var array
     */
    protected $encoderContext = [
        JsonEncode::OPTIONS        => JSON_PRETTY_PRINT,
        XmlEncoder::FORMAT_OUTPUT  => true,
        XmlEncoder::ROOT_NODE_NAME => 'export',
        'yaml_indent'              => 0,
        'yaml_inline'              => 10,
    ];

    /**
     * Constructs a new Export service
     */
    public function __construct(
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * Builds an export
     *
     * @throws Exception
     */
    public function export(array $options): ExportResponse
    {
        $this->logger->info(json_encode($options, JSON_PARTIAL_OUTPUT_ON_ERROR));

        if ($options['type'] == 'simple') {
            return match ($options['format']) {
                'json', 'xml', 'yaml' => $this->simpleUseSerializer($options),
                'csv', 'ods', 'xlsx' => $this->simpleUsePhpSpreadsheet($options),
                default => throw new Exception('Invalid format: ' . $options['format']),
            };
        } else {
            switch ($options['format']) {
                case 'json':
                case 'xml':
                case 'yaml':
                    return $this->fullUseSerializer($options);

                case 'csv':
                case 'ods':
                case 'xlsx':
                    throw new Exception('Format "' . $options['format'] . '" does not support full exports');

                default:
                    throw new Exception('Invalid format: ' . $options['format']);
            }
        }
    }

    /**
     * Uses the Symfony serializer to export data
     */
    protected function fullUseSerializer(array $options): ExportResponse
    {
        $data = (new ExportContainer())
            ->setBoxes($this->em->getRepository(Box::class)->getSortedByDisplayLabel())
            ->setBoxModels($this->em->getRepository(BoxModel::class)->getSortedByDisplayLabel())
            ->setLocations($this->em->getRepository(Location::class)->getSortedByDisplayLabel());

        $encoders = [
            new JsonEncoder(),
            new XmlEncoder(),
            new YamlEncoder(),
        ];
        $normalizers = [
            new EntityNormalizer(),
            new ObjectNormalizer(),
        ];
        $serializer = new Serializer($normalizers, $encoders);

        $data = $serializer->serialize(
            $data,
            $options['format'],
            $this->encoderContext
        );

        $this->logger->info($data);

        return (new ExportResponse())
            ->setFormat($options['format'])
            ->setData($data);
    }

    /**
     * Uses PHPSpreadsheet to export data
     *
     * @throws Exception
     */
    protected function simpleUsePhpSpreadsheet(array $options): ExportResponse
    {
        $boxes = $this->em->getRepository(Box::class)->getSortedByDisplayLabel();

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
            throw new Exception('No data to export');
        }
        $headerRow = array_keys($data[array_key_first($data)]);

        $this->logger->info(var_export($data, true));

        $spreadsheet = new Spreadsheet();
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
        $writer = match ($options['format']) {
            'csv' => new CsvWriter($spreadsheet),
            'ods' => new OdsWriter($spreadsheet),
            'xlsx' => new XlsxWriter($spreadsheet),
            default => throw new Exception('Invalid format: ' . $options['format']),
        };

        $writer->save($filename);

        return (new ExportResponse())
            ->setFormat($options['format'])
            ->setFilename($filename);
    }

    /**
     * Uses the Symfony serializer to export data
     */
    protected function simpleUseSerializer(array $options): ExportResponse
    {
        $data = [
            'boxes' => $this->em->getRepository(Box::class)->getSortedByDisplayLabel(),
        ];

        $encoders = [
            new JsonEncoder(),
            new XmlEncoder(),
            new YamlEncoder(),
        ];
        $normalizers = [new BoxNormalizer(true)];
        $serializer = new Serializer($normalizers, $encoders);

        $context = $this->encoderContext;

        $context[AbstractNormalizer::ATTRIBUTES] = [
            'displayId',
            'label',
            'description',
            'boxModel' => ['label'],
            'location' => ['displayLabel'],
        ];

        $data = $serializer->serialize(
            $data,
            $options['format'],
            $context
        );

        $this->logger->info($data);

        return (new ExportResponse())
            ->setFormat($options['format'])
            ->setData($data);
    }
}
