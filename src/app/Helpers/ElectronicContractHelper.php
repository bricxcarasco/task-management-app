<?php

/**
 * Common Helper
 *
 * @author yns
 */

namespace App\Helpers;

use App\Models\Document;
use App\Objects\FilepondFile;
use Illuminate\Support\Facades\Storage;

/**
 * App\Helpers\ElectronicContractHelper
 *
 * Reusable function that can be used as a helper for Electronic Contracts
 */
class ElectronicContractHelper
{
    /**
     * Get file path from request data
     *
     * @param array $requestData
     * @return string|null
     */
    public static function getRequestFilePath($requestData)
    {
        $disk = config('bphero.private_bucket');
        $storage = Storage::disk($disk);

        // Setup stream wrapper for s3 adapter
        if ($storage->isS3Adapter($disk)) {
            $storage->initializeS3Stream($disk);
        }

        // Get temporary uploaded file
        if (!empty($requestData['local_file'])) {
            $filepond = new FilepondFile($requestData['local_file'], true, $disk);

            return $filepond->getStreamPath();
        }

        // Get file from document management
        if (!empty($requestData['selected_document_id'])) {
            /** @var Document */
            $document = Document::findOrFail($requestData['selected_document_id']);

            // Prepare path
            $targetFilePath = CommonHelper::removeMainDirectoryPath($document->storage_path);

            return $storage->getStreamPath($targetFilePath, $disk);
        }

        return null;
    }
}
