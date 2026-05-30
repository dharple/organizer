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

/**
 * Represents an export file ready for download.
 */
class ExportResponse
{
    /**
     * Maps format extensions to MIME types.
     *
     * @var array<string, string>
     */
    protected const MIME_TYPES = [
        'csv'  => 'text/csv',
        'json' => 'application/json',
        'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml'  => 'application/xml',
        'yaml' => 'application/yaml',
    ];

    /**
     * The path to the temporary export file.
     */
    protected string $filename;

    /**
     * The file format extension.
     */
    protected string $format = 'bin';

    /**
     * Returns the MIME content type for this export.
     */
    public function getContentType(): string
    {
        return static::MIME_TYPES[$this->format] ?? 'application/octet-stream';
    }

    /**
     * Returns the path to the temporary export file.
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Returns the file format extension.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Returns a suggested download filename including timestamp and extension.
     */
    public function getSuggestedFilename(): string
    {
        return 'export-' . date('YmdHi') . '.' . $this->getFormat();
    }

    /**
     * Writes data to a temporary file and stores the path.
     */
    public function setData(string $data): self
    {
        $this->filename = tempnam(sys_get_temp_dir(), 'export_data_');
        file_put_contents($this->filename, $data);
        return $this;
    }

    /**
     * Sets the path to the export file directly.
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * Sets the file format extension.
     */
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }
}
