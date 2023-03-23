<?php

namespace App\Services;

class CsvService
{
    /**
     * Generate CSV file
     *
     * @param array $columns - column headers
     * @param array $rows - row data of CSV
     * @param bool $closeStream - automatically close file stream
     *
     * @return resource
     */
    public function generateCSV($columns, $rows, $closeStream = true)
    {
        // Initialize stream
        $stream = 'php://output';

        // Set max memory of temporary file to: 5MB
        if (!$closeStream) {
            $maxMemory = 5 * 1024 * 1024;
            $stream = "php://temp/maxmemory:$maxMemory";
        }

        // Start file stream
        /** @var resource */
        $file = fopen($stream, 'w');

        // Put data in CSV file
        /** @var array */
        $columns = array_map('App\Helpers\CommonHelper::convertEncodingToUtf8', $columns);
        fputcsv($file, $columns);

        foreach ($rows as $row) {
            /** @var array */
            $row = array_map('App\Helpers\CommonHelper::convertEncodingToUtf8', $row);
            fputcsv($file, $row);
        }

        if ($closeStream) {
            fclose($file);
        }

        return $file;
    }

    /**
     * Export CSV file
     *
     * @param array $columns - column headers
     * @param array $rows - row data of CSV
     * @param string $fileName - filename of file to be downloaded
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function export($columns, $rows, $fileName)
    {
        // Setup headers
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Setup stream callback of csv data
        $csvData = function () use ($columns, $rows) {
            $this->generateCSV($columns, $rows);
        };

        // Response stream
        return response()->stream($csvData, 200, $headers);
    }
}
