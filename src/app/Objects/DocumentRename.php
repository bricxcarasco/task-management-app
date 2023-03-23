<?php

namespace App\Objects;

use App\Enums\Document\DocumentTypes;
use App\Models\Document;

class DocumentRename
{
    /**
     * Constructor function
     *
     * @param \App\Models\Document $document
     * @param string $newName
     * @return void
     */
    public function __construct(Document $document, String $newName)
    {
        /** @phpstan-ignore-next-line */
        $this->document = $document;
        /** @phpstan-ignore-next-line */
        $this->newName = $newName;
    }

    /**
     * Rename document file
     *
     * @param string $extension
     * @return mixed
     */
    public function renameFile($extension)
    {
        $newStoragePath = null;
        try {
            /** @phpstan-ignore-next-line */
            $documentName = $this->newName;
            /** @phpstan-ignore-next-line */
            $document = $this->document;

            if (
                $document->document_type !== DocumentTypes::FOLDER
                && !is_null($document->mime_type)
            ) {
                // Construct document type based on mime type
                $documentNameWithExt = "{$documentName}.{$extension}";

                // Update storage path
                $newStoragePath = self::updateFileStoragePath($document, $documentNameWithExt);

                // Update document name
                $document->update(['document_name' => $documentNameWithExt]);
            }

            return $newStoragePath;
        } catch (\Exception $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Rename document folder.
     *
     * @return mixed
     */
    public function renameFolder()
    {
        try {
            /** @phpstan-ignore-next-line */
            $document = $this->document;
            /** @phpstan-ignore-next-line */
            $documentName = $this->newName;

            // Update document folder name
            $document->update(['document_name' => $documentName]);

            return;
        } catch (\Exception $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Construct & update new storage path based on updated folder name.
     *
     * @param \App\Models\Document $document
     * @param string $newFileName New file name with extension
     * @return string|null
     */
    private static function updateFileStoragePath($document, $newFileName)
    {
        $storagePathsArray = explode('/', $document->storage_path);
        $lastArrayKey = array_key_last($storagePathsArray);
        $storagePathsArray[$lastArrayKey] = $document->id . '_' . $newFileName;
        $newStoragePath = implode('/', $storagePathsArray);

        //update the database storage path
        if ($document->update(['storage_path' => $newStoragePath])) {
            //get the valid s3 bucket storage path
            $newStoragePath = explode("/", $newStoragePath);
            $newStoragePath = array_splice($newStoragePath, 1, 4);
            $newStoragePath = implode("/", $newStoragePath);

            return $newStoragePath;
        }

        return null;
    }

    /**
     * Construct & update new storage path based on updated folder name.
     *
     * @param \App\Models\Document $document
     * @param string $currentDirectory
     * @param string $oldFolderName
     * @param string $newFolderName
     * @return void
     */
    private static function updateFolderStoragePath($document, $currentDirectory, $oldFolderName, $newFolderName)
    {
        $storagePathsArray = explode('/', $document->storage_path);
        $newStoragePath = null;

        if (in_array($oldFolderName, $storagePathsArray)) {
            $hasSameFolderNames = (count(array_keys($storagePathsArray, $oldFolderName)) > 1);

            if ($hasSameFolderNames) {
                $dirStrLength = strlen($currentDirectory);
                $subDirectoryPath = substr($document->storage_path, $dirStrLength);
                $subDirectoryPathArray = explode('/', $subDirectoryPath);
                $key = array_search($oldFolderName, $subDirectoryPathArray);
                $subDirectoryPathArray[$key] = $newFolderName;
                $newStoragePath = $currentDirectory . implode('/', $subDirectoryPathArray);
            } else {
                $key = array_search($oldFolderName, $storagePathsArray);
                $storagePathsArray[$key] = $newFolderName;
                $newStoragePath = implode('/', $storagePathsArray);
            }

            if (!empty($newStoragePath)) {
                $document->update(['storage_path' => $newStoragePath]);
            }

            if (!empty($document->directory)) {
                self::updateFolderStoragePath($document->directory, $currentDirectory, $oldFolderName, $newFolderName);
            }
        }
    }
}
