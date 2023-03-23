<?php

namespace App\Http\Controllers\ElectronicContract;

use App\Http\Controllers\Controller;
use App\Enums\ServiceSelectionTypes;
use App\Http\Requests\ElectronicContract\ManualEmailRegisterRequest;
use App\Http\Requests\Document\ProcessUploadRequest;
use App\Http\Requests\Document\ProcessChunkRequest;
use App\Http\Requests\Document\RevertUploadRequest;
use App\Models\Neo;
use App\Models\ElectronicContract;
use App\Objects\ServiceSelected;
use Illuminate\Http\UploadedFile;
use App\Objects\FilepondFile;
use App\Helpers\CommonHelper;

class ElectronicContractController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        if (empty($rio)) {
            abort(404);
        }

        // Get service selected
        $service = ServiceSelected::getSelected();

        if (empty($service) || ($service->type === 'NEO' && $service->data->is_member)) {
            abort(403);
        }

        $service->email_addresses = [
            $user->email
        ];

        if ($service->type === ServiceSelectionTypes::NEO) {
            $neo = Neo::findOrFail($service->data->id);

            if (empty($neo)) {
                abort(404);
            }

            $service->email_addresses = $neo->emails->pluck('content');
        }

        $availableSlot = ElectronicContract::availableSlot($service);

        return view('electronic-contracts.register', compact('service', 'availableSlot'));
    }

    /**
     * Validate manual registration of recipients
     *
     * @param \App\Http\Requests\ElectronicContract\ManualEmailRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manualRecipientRegister(ManualEmailRegisterRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        return response()->respondSuccess();
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
                $result = FilepondFile::storeTemporaryFile($file);

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
}
