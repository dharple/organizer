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
use App\Support\Serializer\BoxNormalizer;
use App\Support\Serializer\EntityNormalizer;
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
use Symfony\Component\Serializer\NameConverter\SnakeCaseToCamelCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Exports box and related data in a variety of formats.
 */
class ExportService
{
    /**
     * Default encoder context options for serialization.
     *
     * @var array<string, mixed>
     */
    protected array $encoderContext = [
        JsonEncode::OPTIONS        => JSON_PRETTY_PRINT,
        XmlEncoder::FORMAT_OUTPUT  => true,
        XmlEncoder::ROOT_NODE_NAME => 'export',
        YamlEncoder::YAML_INDENT   => 0,
        YamlEncoder::YAML_INLINE   => 10,
    ];

    /**
     * Constructor.
     */
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * Exports data according to the given options and returns the response.
     *
     * @param array<string, string> $options
     * @throws Exception
     */
    public function export(array $options): ExportResponse
    {
        $this->logger->info(json_encode($options, JSON_PARTIAL_OUTPUT_ON_ERROR));

        if ($options['type'] == 'simple') {
            return match ($options['format']) {
                'json', 'xml', 'yaml' => $this->simpleUseSerializer($options),
                'csv', 'ods', 'xlsx'  => $this->simpleUsePhpSpreadsheet($options),
                default => throw new Exception('Invalid format: ' . $options['format']),
            };
        }

        return match ($options['format']) {
            'json', 'xml', 'yaml' => $this->fullUseSerializer($options),
            'csv', 'ods', 'xlsx'  => throw new Exception('Format "' . $options['format'] . '" does not support full exports'),
            default => throw new Exception('Invalid format: ' . $options['format']),
        };
    }

    /**
     * Exports all entities using the Symfony Serializer.
     *
     * @param array<string, string> $options
     */
    protected function fullUseSerializer(array $options): ExportResponse
    {
        $boxes     = Box::sortedByDisplayLabel()->get()->all();
        $boxModels = BoxModel::orderBy('label')->get()->all();
        $locations = Location::all()->sortBy(fn(Location $l) => $l->getDisplayLabel())->values()->all();

        $data = (new ExportContainer())
            ->setBoxes($boxes)
            ->setBoxModels($boxModels)
            ->setLocations($locations);

        $encoders = [new JsonEncoder(), new XmlEncoder(), new YamlEncoder()];
        $normalizers = [new EntityNormalizer(), new ObjectNormalizer(null, new SnakeCaseToCamelCaseNameConverter())];
        $serializer = new Serializer($normalizers, $encoders);

        $serialized = $serializer->serialize($data, $options['format'], $this->encoderContext);

        $this->logger->info($serialized);

        return (new ExportResponse())
            ->setFormat($options['format'])
            ->setData($serialized);
    }

    /**
     * Exports a simple box list using PHPSpreadsheet.
     *
     * @param array<string, string> $options
     * @throws Exception
     */
    protected function simpleUsePhpSpreadsheet(array $options): ExportResponse
    {
        $boxes = Box::sortedByDisplayLabel()->get()->all();

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

        $filename = tempnam(sys_get_temp_dir(), 'export_spreadsheet_');
        $writer = match ($options['format']) {
            'csv'  => new CsvWriter($spreadsheet),
            'ods'  => new OdsWriter($spreadsheet),
            'xlsx' => new XlsxWriter($spreadsheet),
            default => throw new Exception('Invalid format: ' . $options['format']),
        };
        $writer->save($filename);

        return (new ExportResponse())
            ->setFormat($options['format'])
            ->setFilename($filename);
    }

    /**
     * Exports a simple box list using the Symfony Serializer.
     *
     * @param array<string, string> $options
     */
    protected function simpleUseSerializer(array $options): ExportResponse
    {
        $data = ['boxes' => Box::sortedByDisplayLabel()->get()->all()];

        $encoders = [new JsonEncoder(), new XmlEncoder(), new YamlEncoder()];
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

        $serialized = $serializer->serialize($data, $options['format'], $context);

        $this->logger->info($serialized);

        return (new ExportResponse())
            ->setFormat($options['format'])
            ->setData($serialized);
    }
}
