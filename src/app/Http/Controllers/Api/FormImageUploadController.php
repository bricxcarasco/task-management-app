<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\CommonHelper;
use App\Http\Requests\Document\ProcessUploadRequest;
use App\Http\Requests\Document\ProcessChunkRequest;
use App\Http\Requests\FilePond\RestoreUploadRequest;
use App\Http\Requests\Document\RevertUploadRequest;
use App\Objects\FilepondFile;
use Illuminate\Http\UploadedFile;
use App\Traits\FilePondUploadable;

class FormImageUploadController extends Controller
{
    use FilePondUploadable;

    /**
     * Controller constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->filepondDisk = config('bphero.public_bucket');
    }

    /**
     * Process Upload API Endpoint
     *
     * @param \App\Http\Requests\Document\ProcessUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processUpload(ProcessUploadRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $files = $requestData['upload_file'];

        // Check if data sent is a file
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = FilepondFile::storeTemporaryFile($file, config('bphero.public_bucket'));

                // Return failed save
                if ($result === false) {
                    return response()->respondInternalServerError(['Could not save file.']);
                }

                // Return server ID
                return response($result, 200, [
                    'Content-Type' => 'text/plain',
                ]);
            }

            // Handle Filepond File Metadata (usually on chunk upload)
            if (CommonHelper::isJson($file)) {
                $path = FilepondFile::generateTemporaryDirectory();
                $serverCode = FilepondFile::toServerCode($path);

                // Return server ID
                return response($serverCode, 200, [
                    'Content-Type' => 'text/plain',
                ]);
            }
        }

        // Return failed save
        return response()->respondInternalServerError(['Could not save file.']);
    }

    /**
     * Process chunk uploaded file
     *
     * @param \App\Http\Requests\Document\ProcessChunkRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processChunk(ProcessChunkRequest $request)
    {
        try {
            $requestData = $request->validated();

            // Initialize filepond file
            $filepond = new FilepondFile($requestData['patch'], true);

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
     * Revert uploaded file
     *
     * @param \App\Http\Requests\Document\RevertUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function revertUpload(RevertUploadRequest $request)
    {
        $requestData = $request->validated();

        // Initialize filepond file
        $filepond = new FilepondFile($requestData['code'], true);

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
}
