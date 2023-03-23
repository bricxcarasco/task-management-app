<?php

namespace App\Objects;

use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;

class FilepondImage extends FilepondFile
{
    /**
     * Store file to a temporary directory
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string|null $disk
     * @param bool $shouldDownsize
     * @return string|false
     */
    public static function storeTemporaryFile(UploadedFile $file, $disk = null, $shouldDownsize = false)
    {
        $storageDisk = $disk ?? config('bphero.private_bucket');

        // Generate temporary directory
        $tempPath = static::generateTemporaryDirectory();

        // Get filename
        $fileName = $file->getClientOriginalName();

        // Store file in temporary directory
        $result = $file->storeAs($tempPath, $fileName, $storageDisk);

        // Return false on store error
        if ($result === false) {
            return false;
        }

        // Get file path
        $filePath = $tempPath . '/' . $fileName;

        // Downsize images
        if ($shouldDownsize) {
            ImageHelper::downsize($filePath, [
                'file_name' => $fileName,
                'file_size' => $file->getSize(),
                'disk' => $storageDisk,
            ]);
        }

        // Generate server code from path
        return static::toServerCode($tempPath);
    }

    /**
     * Compile/merge all patch file chunks into a single file
     *
     * @param string $fileName
     * @param bool $shouldDownsize
     * @return string Stream path used for output file
     */
    public function compilePatchFiles($fileName, $shouldDownsize = false)
    {
        // Compile patch files
        $streamPath = parent::compilePatchFiles($fileName);

        // Skip optimization
        if (!$shouldDownsize) {
            return $streamPath;
        }

        // Get temporary file path
        $filePath = $this->getFilePath($fileName);

        // Guard clause for non-existing file path
        if (empty($filePath)) {
            return $streamPath;
        }

        // Downsize images
        ImageHelper::downsize($filePath, [
            'file_name' => $fileName,
            'disk' => $this->disk,
        ]);

        return $streamPath;
    }
}
