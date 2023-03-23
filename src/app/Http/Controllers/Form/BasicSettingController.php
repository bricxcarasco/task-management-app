<?php

namespace App\Http\Controllers\Form;

use App\Enums\ServiceSelectionTypes;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\FilePond\LoadFileRequest;
use App\Http\Requests\FilePond\ProcessChunkRequest;
use App\Http\Requests\Form\BasicSettingRequest;
use App\Http\Requests\Form\BasicSettingProcessUploadRequest;
use App\Models\FormBasicSetting;
use App\Models\User;
use App\Objects\ClassifiedImages;
use App\Objects\FilepondFile;
use App\Objects\FilepondImage;
use App\Objects\ServiceSelected;
use App\Traits\FilePondUploadable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Session;

class BasicSettingController extends Controller
{
    use FilePondUploadable;

    /**
     * Controller constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->filepondDisk = config('bphero.form_service_disk');
    }

    /**
     * Basic settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get subject selected session
        $service = ServiceSelected::getSelected();

        //Get user profile photo
        $userImage = FormBasicSetting::getServiceProfilePhoto($service);

        // Get basic settings
        $basicSettings = FormBasicSetting::serviceSetting($service)->first();
        if (empty($basicSettings)) {
            $basicSettings = FormBasicSetting::getSettingFromRioNeo($service);
        }

        return view('forms.basic-settings.index', compact(
            'service',
            'basicSettings',
            'userImage'
        ));
    }

    /**
     * Create or Update basic settings
     *
     * @param \App\Http\Requests\Form\BasicSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveBasicSettings(BasicSettingRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var User */
        $user = auth()->user();
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        //Get user image
        $userImage = FormBasicSetting::getServiceProfilePhoto($service);

        // Fetch basic settings
        $basicSettings = FormBasicSetting::serviceSetting($service)->first();

        DB::beginTransaction();

        try {
            if (isset($requestData['image'])) {
                $filepond = new FilepondFile($requestData['image'], true, $this->filepondDisk);
                $disk = Storage::disk($this->filepondDisk);

                // Get image path and directories
                $rioImagePath = config('bphero.rio_basic_settings_storage_path');
                $neoImagePath = config('bphero.neo_basic_settings_storage_path');
                $basicSettingsImagePath = config('app.url') . '/storage/';
                $filename = $service->data->id . '.png';
                $imagePath = $service->type === ServiceSelectionTypes::RIO
                    ? '/' . $rioImagePath . $service->data->id
                    : '/' . $neoImagePath . $service->data->id;

                // Delete existing image before saving new
                if ($disk->exists($imagePath . '/' . $filename)) {
                    $disk->delete($imagePath . '/' . $filename);
                }

                // Transfer temporary file to permanent directory
                $fileinfo = $filepond->transferFile($imagePath, $filename, false);

                // Update image path in basic settings information
                $requestData['image'] = $basicSettingsImagePath . $fileinfo['path'];
            } else {
                if ($requestData['delete_existing']) {
                    $requestData['image'] = $userImage;
                }
            }

            if (empty($basicSettings)) {
                // Create new basic settings record
                $basicSettings = new FormBasicSetting();
                $basicSettings->fill($requestData);
                $basicSettings->rio_id = $service->type === ServiceSelectionTypes::RIO ? $rio->id : null;
                $basicSettings->neo_id = $service->type === ServiceSelectionTypes::NEO ? $service->data->id : null;
                $basicSettings->created_rio_id = $rio->id;

                // Save new record
                $basicSettings->save();
            } else {
                // Update basic settings record
                $basicSettings->fill($requestData);
                $basicSettings->save();
            }

            // Commit database changes
            DB::commit();

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);

            return response()->respondInternalServerError([$exception->getMessage()]);
        }
    }

    /**
     * Redirect to forms list page on successful update of basic settings
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveSuccess()
    {
        return redirect()
            ->route('forms.quotations.index')
            ->withAlertBox('success', __('Basic information has been set'));
    }

    /**
     * Process Upload API Endpoint
     *
     * @param \App\Http\Requests\Form\BasicSettingProcessUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processUpload(BasicSettingProcessUploadRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $files = $requestData['image'];

        // Check if data sent is a file
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = FilepondImage::storeTemporaryFile($file, $this->filepondDisk, true);

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
                $path = FilepondImage::generateTemporaryDirectory();
                $serverCode = FilepondImage::toServerCode($path);

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
     * @param \App\Http\Requests\FilePond\ProcessChunkRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processChunk(ProcessChunkRequest $request)
    {
        try {
            $requestData = $request->validated();

            // Initialize filepond file
            $filepond = new FilepondImage($requestData['patch'], true, $this->filepondDisk);

            // Save patch data to a temporary file
            if (!$filepond->exists($requestData['filename'])) {
                $filepond->createTemporaryPatchFile($requestData['offset']);

                // Guard clause for unfinished patch upload
                if (!$filepond->isPatchUploadComplete($requestData['length'])) {
                    return response()->respondNoContent();
                }

                // Compile patch files to a single file
                $filepond->compilePatchFiles($requestData['filename'], true);
            }

            return response()->respondNoContent();
        } catch (\Exception $exception) {
            return response()->respondInternalServerError([$exception->getMessage()]);
        }
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

        // Prepare filename
        $targetFilename = ClassifiedImages::removePrefix($fileName);
        if (!empty($targetFilename)) {
            $fileName = $targetFilename;
        }

        return response($file)
            ->withHeaders([
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
    }
}
