<?php

/**
 * Datatable Requestable Trait
 *
 * @author yns
 */

namespace App\Traits;

use App\Helpers\CommonHelper;
use App\Http\Requests\FilePond\LoadFileRequest;
use App\Http\Requests\FilePond\ProcessChunkRequest;
use App\Http\Requests\FilePond\RestoreUploadRequest;
use App\Http\Requests\FilePond\RetryChunkRequest;
use App\Http\Requests\FilePond\RevertUploadRequest;
use App\Objects\FilepondFile;
use Illuminate\Support\Facades\Storage;

/**
 * App\Traits\FilePondUploadable
 *
 * @author yns
 */
trait FilePondUploadable
{
    /**
     * Target disk of Filepond classes
     *
     * @var string|null
     */
    public $filepondDisk = null;

    /**
     * Process chunk uploaded file
     *
     * @param \App\Http\Requests\FilePond\ProcessChunkRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processChunk(ProcessChunkRequest $request)
    {
        try {
            $requestData = $request->validated();

            // Initialize filepond file
            $filepond = new FilepondFile($requestData['patch'], true, $this->filepondDisk);

            // Save patch data to a temporary file
            if (!$filepond->exists($requestData['filename'])) {
                $filepond->createTemporaryPatchFile($requestData['offset']);

                // Guard clause for unfinished patch upload
                if (!$filepond->isPatchUploadComplete($requestData['length'])) {
                    return response()->respondNoContent();
                }

                // Compile patch files to a single file
                $filepond->compilePatchFiles($requestData['filename']);
            }

            return response()->respondNoContent();
        } catch (\Exception $exception) {
            return response()->respondInternalServerError([$exception->getMessage()]);
        }
    }

    /**
     * Retry chunk offset upload
     *
     * @param \App\Http\Requests\FilePond\RetryChunkRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function retryChunk(RetryChunkRequest $request)
    {
        $requestData = $request->validated();

        // Initialize filepond file
        $filepond = new FilepondFile($requestData['patch'], true, $this->filepondDisk);

        // Check if filepond file exists
        if (!$filepond->exists()) {
            return response()->respondNotFound();
        }

        // Clear temporary directory
        $filepond->clearDirectory();

        return response()->respondNoContent([
            'Upload-Offset' => 0,
        ]);
    }

    /**
     * Revert uploaded file
     *
     * @param \App\Http\Requests\FilePond\RevertUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function revertUpload(RevertUploadRequest $request)
    {
        $requestData = $request->validated();

        // Initialize filepond file
        $filepond = new FilepondFile($requestData['code'], true, $this->filepondDisk);

        // Check if filepond file exists
        if (!$filepond->exists()) {
            return response()->respondNotFound();
        }

        // Remove temporary directory and its file/s
        if ($filepond->removeDirectory()) {
            return response()->respondSuccess();
        }

        return response()->respondInternalServerError();
    }

    /**
     * Restore temp uploaded file
     *
     * @param \App\Http\Requests\FilePond\RestoreUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function restoreUpload(RestoreUploadRequest $request)
    {
        $requestData = $request->validated();

        // Initialize filepond file
        $filepond = new FilepondFile($requestData['code'], true, $this->filepondDisk);

        // Check if filepond file exists
        if (!$filepond->exists()) {
            return response()->respondNotFound();
        }

        return $filepond->respondFile();
    }

    /**
     * Load already uploaded file to filepond
     *
     * @param \App\Http\Requests\FilePond\LoadFileRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function loadFile(LoadFileRequest $request)
    {
        $requestData = $request->validated();
        $storage = Storage::disk($this->filepondDisk);

        // Decode url
        $decodedPath = rawurldecode($requestData['path']);
        $targetPath = CommonHelper::removeMainDirectoryPath($decodedPath);

        // Check if file is existing
        if (!$storage->exists($targetPath)) {
            return response()->respondNotFound();
        }

        // Prepare information
        $file = $storage->get($targetPath);
        $mimeType = $storage->mimeType($targetPath);
        $fileName = CommonHelper::getBasename($targetPath);

        return response($file)
            ->withHeaders([
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
    }
}
