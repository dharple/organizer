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
use App\Serializer\Normalizer\BoxNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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
     * @var ObjectNormalizer
     */
    protected $objectNormalizer;

    /**
     * Constructs a new Export service
     */
    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        ObjectNormalizer $objectNormalizer
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->objectNormalizer = $objectNormalizer;
    }

    /**
     * Builds an export
     */
    public function export(array $options): ExportResponse
    {
        $this->logger->info(json_encode($options));

        $boxes = $this->em->getRepository(Box::class)->getSorted();

        switch ($options['format']) {
            case 'json':
            case 'xml':
                return $this->useSerializer($options, $boxes);

            case 'csv':
            case 'ods':
            case 'xlsx':
                return $this->usePhpSpreadsheet($options, $boxes);

            default:
                throw new \Exception('Invalid format: ' . $options['format']);
        }
    }

    /**
     * Uses PHPSpreadsheet to export data
     */
    protected function usePhpSpreadsheet(array $options, array $boxes): ExportResponse
    {
        $normalizers = [new BoxNormalizer($this->objectNormalizer), $this->objectNormalizer];
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
    protected function useSerializer(array $options, array $boxes): ExportResponse
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder()];
        $normalizers = [new BoxNormalizer($this->objectNormalizer), $this->objectNormalizer];
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
