<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Enums\Document\DocumentTypes;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Enums\Document\DocumentShareType;
use App\Exceptions\ServiceSessionNotFoundException;
use App\Helpers\CommonHelper;
use App\Http\Requests\Document\DocumentListRequest;
use App\Http\Requests\Document\DocumentRenameRequest;
use App\Http\Requests\Document\CreateFolderRequest;
use App\Http\Requests\Document\SaveSettingRequest;
use App\Http\Requests\Document\DocumentAccessRequest;
use App\Http\Requests\Document\DocumentSearchShareSettingRequest;
use App\Http\Resources\Document\DocumentListResource;
use App\Http\Resources\Document\DocumentResource;
use App\Http\Resources\Document\DocumentPermittedListResource;
use App\Http\Resources\Document\DocumentConnectedListResource;
use App\Http\Requests\Document\DocumentRequest;
use App\Http\Requests\Document\DocumentShareSettingRequest;
use App\Http\Requests\Document\ProcessUploadRequest;
use App\Http\Requests\Document\ProcessChunkRequest;
use App\Http\Requests\Document\RetryChunkRequest;
use App\Http\Requests\Document\RevertUploadRequest;
use App\Http\Requests\Document\UploadFileRequest;
use App\Http\Requests\Document\DocumentPermittedListRequest;
use App\Models\Document;
use App\Models\DocumentAccess;
use App\Models\Neo;
use App\Models\Notification;
use App\Models\Rio;
use App\Models\RioConnection;
use App\Models\ServiceSetting;
use App\Models\User;
use App\Objects\DocumentRename;
use Carbon\Carbon;
use App\Objects\FilepondFile;
use App\Objects\ServiceSelected;
use App\Objects\TalkSubject;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response as Download;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocumentController extends Controller
{
    /**
     * Display documentation list.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @param int|null $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function list(DocumentListRequest $request, $id = null)
    {
        // Get request data
        $requestData = $request->all();
        $requestData['directory_id'] = $id;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                'data' => $user->rio,
                'type' => ServiceSelectionTypes::RIO
            ]));
        }

        // Get service selected
        $service = json_decode(Session::get('ServiceSelected'));

        // Check for valid directory id
        if (!is_null($requestData['directory_id'])) {
            $folderDetails = Document::where('documents.id', $requestData['directory_id'])->first();

            if (!$folderDetails || isset($folderDetails->mime_type)) {
                return response()->respondNotFound([], __('Invalid Document ID'));
            }
        }

        // Fetch explicitly typed document list
        if (!empty($requestData['document_type'])) {
            $documentCollection = Document::documentList($service, $requestData)
                ->paginate(config('bphero.paginate_count'));

            return response()->respondSuccess([
                'result' => DocumentListResource::collection($documentCollection),
            ]);
        }

        // Fetch list for all document types
        $folderResult = Document::folderList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));
        $fileResult = Document::fileList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));
        $attachmentResult = Document::attachmentList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));

        return response()->respondSuccess([
            'file_result' => DocumentListResource::collection($fileResult),
            'folder_result' => DocumentListResource::collection($folderResult),
            'attachment_result' => DocumentListResource::collection($attachmentResult),
        ]);
    }

    /**
     * Create document folder.
     *
     * Endpoint: /api/document/folder
     * Method: POST
     *
     * @param \App\Http\Requests\Document\CreateFolderRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function createFolder(CreateFolderRequest $request)
    {
        try {
            // Get validated data
            $requestData = $request->validated();

            /** @var User */
            $user = auth()->user();

            // Create new instance of Document model
            $document = new Document();

            // Service selection session state values
            $service = json_decode(Session::get('ServiceSelected'));

            // Set create new folder data
            $document->fill($requestData);
            $document->document_type = DocumentTypes::FOLDER;

            if ($service->type === ServiceSelectionTypes::RIO) {
                $document->owner_rio_id = $user->rio_id;
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                $document->owner_neo_id = $service->data->id;
            }

            // Check if authorize to handle request
            $this->authorize('allowed', [Document::class, $document]);

            // Save new folder
            $document->save();

            return response()->respondSuccess($document, __('Successfully created folder'));
        } catch (NotFoundHttpException $e) {
            return response()->respondNotFound();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return response()->respondInternalServerError();
        }
    }

    /**
     * Display shared list.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function shared(DocumentListRequest $request)
    {
        // Get request data
        $requestData = $request->all();
        $requestData['for_shared'] = true;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                'data' => $user->rio,
                'type' => ServiceSelectionTypes::RIO
            ]));
        }

        // Get service selected
        $service = json_decode(Session::get('ServiceSelected'));

        // Fetch explicitly typed document list
        if (!empty($requestData['document_type'])) {
            $documentCollection = Document::documentList($service, $requestData)
                ->paginate(config('bphero.paginate_count'));

            return response()->respondSuccess([
                'result' => DocumentListResource::collection($documentCollection),
            ]);
        }

        // Fetch list for all document types
        $folderResult = Document::folderList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));
        $fileResult = Document::fileList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));
        $attachmentResult = Document::attachmentList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));

        return response()->respondSuccess([
            'file_result' => DocumentListResource::collection($fileResult),
            'folder_result' => DocumentListResource::collection($folderResult),
            'attachment_result' => DocumentListResource::collection($attachmentResult),
        ]);
    }

    /**
     * Rename document folder or file name.
     *
     * Endpoint: /api/document/rename/{id}
     * Method: PUT
     *
     * @param \App\Http\Requests\Document\DocumentRenameRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function rename(DocumentRenameRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Get validated data
            $requestData = $request->validated();

            // Get service selected
            $service = json_decode(Session::get('ServiceSelected'));

            /** @var Document */
            $document = Document::whereId($id)->first();

            // Guard clause for non-existing document
            if (empty($document)) {
                return response()->respondNotFound();
            }

            // Check authorization
            $this->authorize('rename', [Document::class, $document, $service]);

            // Setup stream wrapper for s3 adapter
            $disk = Storage::disk(config('bphero.private_bucket'));

            // Initialize object
            $documentObject = new DocumentRename($document, $requestData['document_name']);

            switch ($document->document_type) {
                case DocumentTypes::FOLDER:
                    $documentObject->renameFolder();
                    break;

                default:
                    //Get old database storage path to retrieve
                    $oldStoragePath = $document->storage_path;

                    //Get the valid s3 bucket storage path
                    $oldStoragePath = explode("/", $oldStoragePath);
                    $fileExtension = explode(".", end($oldStoragePath));
                    $oldStoragePath = array_splice($oldStoragePath, 1, 4);
                    $oldStoragePath = implode("/", $oldStoragePath);

                    $newStoragePath = $documentObject->renameFile(end($fileExtension));

                    // Update storage path
                    if (!is_null($newStoragePath) && ($oldStoragePath !== $newStoragePath)) {
                        //Update filename in S3 Bucket
                        if ($disk->exists($oldStoragePath)) {
                            $disk->move($oldStoragePath, $newStoragePath);
                        }
                    }
                    break;
            }

            DB::commit();

            return response()->respondSuccess(null, __('Successfully updated document name'));
        } catch (NotFoundHttpException $e) {
            DB::rollBack();

            return response()->respondNotFound();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Delete a document folder or file.
     *
     * Endpoint: /api/document/delete/{id}
     * Method: DELETE
     *
     * @param \App\Http\Requests\Document\DocumentRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete(DocumentRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            // Get service selected
            $service = json_decode(Session::get('ServiceSelected'));

            /** @var Document */
            $document = Document::whereId($id)->first();
            // Guard clause if non-existing document
            if (empty($document)) {
                return response()->respondNotFound();
            }

            // Check authorization
            $this->authorize('delete', [Document::class, $document, $service]);

            // Delete document
            if ($document->delete()) {
                //Delete affected file/s in s3 bucket
                switch ($document->document_type) {
                    case DocumentTypes::FOLDER:
                        foreach ($this->getFilesToDelete($id) as $storagePath) {
                            $this->deleteS3File($storagePath);
                        }
                        break;

                    default:
                        //Get the valid s3 bucket storage path
                        $storagePath = explode("/", $document->storage_path);
                        $storagePath = array_splice($storagePath, 1, 4);
                        $storagePath = implode("/", $storagePath);

                        $this->deleteS3File($storagePath);
                        break;
                }
            }

            DB::commit();

            return response()->respondSuccess(null, __('Successfully deleted'));
        } catch (NotFoundHttpException $e) {
            DB::rollBack();

            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Save setting for sharing documents.
     *
     * Endpoint: /api/document/save-setting
     * Method: POST
     *
     * @param \App\Http\Requests\Document\SaveSettingRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function saveSetting(SaveSettingRequest $request)
    {
        DB::beginTransaction();
        try {
            /** @var \App\Models\User */
            $user = auth()->user();

            // Get validated data
            $requestData = $request->validated();

            /** @var Document */
            $document = Document::findOrFail($requestData['document_id']);

            // Insert is owner record in document access not exist
            $document->checkOwnerDocumentAccess();

            // Check if authorize to handle request
            $this->authorize('allowed', [Document::class, $document]);

            /** @var DocumentAccess */
            $documentAccess = new DocumentAccess();
            $documentAccess->document_id = $requestData['document_id'];

            switch ($requestData['share_type']) {
                case DocumentShareType::RIO:
                    $documentAccess->rio_id = $requestData['id'];
                    break;

                case DocumentShareType::NEO:
                    $documentAccess->neo_id = $requestData['id'];
                    break;

                case DocumentShareType::NEO_GROUP:
                    $documentAccess->neo_group_id = $requestData['id'];
                    break;
            }

            // Save access for document
            $documentAccess->save();

            DB::commit();

            return response()->respondAccepted($documentAccess);
        } catch (NotFoundHttpException $e) {
            DB::rollback();
            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollback();
            Log::debug($e->getMessage());
            return response()->respondInternalServerError();
        }
    }

    /**
     * Return file details for preview.
     *
     * @param int $id document_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function filePreview(Request $request, $id)
    {
        try {
            $s3Path = '';
            $disk = null;

            /** @var Document */
            $document = Document::whereId($id)->first();

            // Guard clause if non-existing document
            if (empty($document)) {
                return response()->respondNotFound();
            }

            // Get current user session
            $service = ($request->get('service') !== 'chat')
                ? json_decode(Session::get('ServiceSelected'))
                : TalkSubject::getSelected();

            // Check authorization
            $this->authorize('viewFile', [Document::class, $document, $service]);

            if ($document->document_type !== DocumentTypes::FOLDER) {
                self::s3Adapter(config('bphero.public_bucket'));
                self::s3Adapter(config('bphero.private_bucket'));

                $storagePath = explode("/", $document->storage_path);
                $storagePath = array_splice($storagePath, 1, 4);
                $storagePath = implode("/", $storagePath);

                //get file url in S3 Bucket
                if (Storage::disk(config('bphero.public_bucket'))->exists($storagePath)) {
                    $disk = Storage::disk(config('bphero.public_bucket'));
                    $s3Path = $disk->path($storagePath);
                }

                //get file url in S3 Bucket
                if (Storage::disk(config('bphero.private_bucket'))->exists($storagePath)) {
                    $disk = Storage::disk(config('bphero.private_bucket'));
                    $s3Path = $disk->path($storagePath);
                }

                $url = urldecode($s3Path);
                $parsedUrl = parse_url($url);

                // Check if file is existing, throw not found if not existing
                /** @phpstan-ignore-next-line */
                if (!$disk->exists($parsedUrl['path'])) {
                    $response = [
                        'success' => 1,
                        'message' => __('File path specified does not exists.'),
                        'path' => $parsedUrl,
                    ];

                    return response()->json($response, 404);
                }

                // Get file and encode to base64
                /** @phpstan-ignore-next-line */
                $file = $disk->get($parsedUrl['path']);
                /** @phpstan-ignore-next-line */
                $mimeType = $disk->mimeType($parsedUrl['path']);

                // Respond file with proper header content-type
                return response($file)
                    ->withHeaders(['Content-Type' => $mimeType]);
            } else {
                return response()->respondInvalidParameters([
                    'field_name' => 'id',
                    'message' => __('The document id must be a file'),
                ]);
            }
        } catch (NotFoundHttpException $e) {
            $response = [
                'success' => 1,
                'message' => __('File path specified does not exists.'),
            ];

            return response()->json($response, 404);
        } catch (\Exception $e) {
            // Return internal server error if unable to upload
            $response = [
                'success' => 1,
                'message' => $e->getMessage(),
            ];

            return response()->json($response, 500);
        }
    }

    /**
     * Share access to folder or file.
     *
     * Endpoint: /api/document/share-setting
     * Method: GET
     *
     * @param \App\Http\Requests\Document\DocumentShareSettingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function shareSetting(DocumentShareSettingRequest $request)
    {
        DB::beginTransaction();
        try {
            // Get validated data
            $requestData = $request->validated();
            $document_id = $requestData['id'];
            $documentAccess = collect();

            // Initialize owner, notifications and email receivers
            $notifications = [];
            $emailReceivers = [];
            $owner = null;
            $ownerName = null;
            $now = date('Y-m-d H:i:s');

            /** @var User */
            $user = auth()->user();
            if (empty(json_decode(Session::get('ServiceSelected')))) {
                Session::put('ServiceSelected', json_encode([
                    'data' => $user->rio,
                    'type' => ServiceSelectionTypes::RIO
                ]));
            }

            $service = json_decode(Session::get('ServiceSelected'));

            /** @var Document */
            $document = Document::whereId($document_id)->first();

            // Guard clause if non-existing document
            if (empty($document)) {
                return response()->respondNotFound();
            }

            // Get notification destination redirection route
            switch ($document->document_type) {
                case DocumentTypes::FOLDER:
                    $destinationUrl = route('document.shared-folder-file-list', $document->id);
                    break;
                case DocumentTypes::FILE:
                    $destinationUrl = route('document.shared-file-preview-route', $document->id);
                    break;
                default:
                    $destinationUrl = config('app.url');
                    break;
            }

            // Exit if no one to share access to
            if (empty($requestData['rio_id']) && empty($requestData['neo_id']) && empty($requestData['neo_group_id'])) {
                return response()->respondNotFound();
            }

            // Check authorization
            $this->authorize('share', [Document::class, $document, $service]);

            if ($service->type === ServiceSelectionTypes::RIO) {
                // Check if already has a record for the file/folder
                $noAccess = DocumentAccess::where('rio_id', $service->data->id)
                    ->where('document_id', $document_id)
                    ->doesntExist();

                // Include RIO owner for the first time
                if ($noAccess) {
                    $documentAccess->push([
                        'rio_id' => $service->data->id,
                        'document_id' => $document_id,
                    ]);
                }

                // Get owner information
                /** @var Rio */
                $owner = Rio::whereId($service->data->id)->first();
                $ownerName = $owner->full_name . 'さん';
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                // Check if already has a record for the file/folder
                $noAccess = DocumentAccess::where('neo_id', $service->data->id)
                    ->where('document_id', $document_id)
                    ->doesntExist();

                // Include NEO owner for the first time
                if ($noAccess) {
                    $documentAccess->push([
                        'neo_id' => $service->data->id,
                        'document_id' => $document_id,
                    ]);
                }

                // Get owner information
                /** @var Neo */
                $owner = Neo::whereId($service->data->id)->first();
                $ownerName = $owner->organization_name;
            }

            // Guard clause if non-existing owner
            if (empty($owner)) {
                return response()->respondNotFound();
            }

            if (!empty($requestData['rio_id'])) {
                foreach ($requestData['rio_id'] as $rioID) {
                    // Append to email receiver
                    $emailReceivers[] = Rio::whereId($rioID)->firstOrFail();

                    // Append to notifications
                    $notifications[] = [
                        'rio_id' => $rioID,
                        'receive_neo_id' => null,
                        'destination_url' => $destinationUrl,
                        'notification_content' => __('Notification Content - Document Sharing', [
                            'sender_name' => $ownerName
                        ]),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $documentAccess->push([
                        'rio_id' => $rioID,
                        'document_id' => $document->id,
                    ]);
                }
            }

            if (!empty($requestData['neo_id'])) {
                foreach ($requestData['neo_id'] as $neoID) {
                    /** @var Neo */
                    $neo = Neo::whereId($neoID)->firstOrFail();

                    /** @var \App\Models\NeoBelong */
                    $neoOwner = $neo->owner;

                    // Guard clause if non-existing NEO owner
                    if (empty($neoOwner)) {
                        return response()->respondNotFound();
                    }

                    // Append to email receiver
                    $emailReceivers[] = $neo;

                    // Append to notifications
                    $notifications[] = [
                        'rio_id' => $neoOwner->rio_id,
                        'receive_neo_id' => $neoID,
                        'destination_url' => $destinationUrl,
                        'notification_content' => __('Notification Content - Document Sharing', [
                            'sender_name' => $ownerName
                        ]),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $documentAccess->push([
                        'neo_id' => $neoID,
                        'document_id' => $document_id,
                    ]);
                }
            }

            if (!empty($requestData['neo_group_id'])) {
                foreach ($requestData['neo_group_id'] as $neoGroupID) {
                    $documentAccess->push([
                        'neo_group_id' => $neoGroupID,
                        'document_id' => $document_id,
                    ]);
                }
            }

            // Insert bulk document accesses
            $document->document_accesses()->createMany($documentAccess);

            // Insert bulk notifications
            Notification::insert($notifications);

            // Send email to shared document receivers
            $document->sendEmailToSharedDocumentReceiver($owner, $emailReceivers);

            DB::commit();

            return response()->respondSuccess(null, __('Folders have been shared'));
        } catch (NotFoundHttpException $e) {
            DB::rollback();

            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollback();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get shared link per document ID.
     *
     * Endpoint: /api/document/shared-link/{document}
     * Method: GET
     *
     * @param Document $document
     *
     * @return \Illuminate\Http\Response
     */
    public function sharedLink(Document $document)
    {
        try {
            // Check if authorize to access document
            $this->authorize('documentAccess', [Document::class, $document]);

            $documentResource = new DocumentResource($document);

            return response()->respondSuccess(url("/document/shared-link/check-access/{$documentResource->id}"));
        } catch (NotFoundHttpException $e) {
            return response()->respondNotFound();
        } catch (\Exception $e) {
            return response()->respondInternalServerError($e);
        }
    }

    /**
     * Invite connected rio user to group
     *
     * @param \App\Http\Requests\Document\UploadFileRequest $request
     * @return \Illuminate\Http\Response|void|null
     */
    public function uploadFile(UploadFileRequest $request)
    {
        try {
            DB::beginTransaction();

            // Get request data
            $requestData = $request->validated();

            /** @var User */
            $user = auth()->user();

            if (empty(json_decode(Session::get('ServiceSelected')))) {
                Session::put('ServiceSelected', json_encode([
                    'data' => $user->rio,
                    'type' => ServiceSelectionTypes::RIO
                ]));
            }

            $service = ServiceSelected::getSelected();

            // Get storage info in service settings table
            $serviceSetting = ServiceSetting::serviceSettingInfo($service)->firstOrFail();
            $storageInfo = $serviceSetting->storage_info;

            //Check if the target directory_id is not a folder
            if (isset($requestData['directory_id']) || !empty($requestData['directory_id'])) {
                /** @var Document */
                $directory = Document::whereId($requestData['directory_id'])->first();

                // Guard clause if non-existing directory id
                if (empty($directory)) {
                    return response()->respondNotFound();
                }

                if ($directory->document_type !== DocumentTypes::FOLDER) {
                    return response()->respondInvalidParameters([
                        'field_name' => 'directory_id',
                        'message' => __('The document id must be a folder'),
                    ]);
                }
            }

            foreach ($requestData['code'] as $fileCode) {
                // Create file data in database
                $documentFile = new Document();

                $documentFile->document_type = DocumentTypes::FILE;

                if ($service->type === ServiceSelectionTypes::RIO) {
                    $documentFile->owner_rio_id = $service->data->id;
                }

                if ($service->type === ServiceSelectionTypes::NEO) {
                    $documentFile->owner_neo_id = $service->data->id;
                }

                $documentFile->fill($requestData);

                // Check if authorize to handle request
                $this->authorize('uploadFile', [Document::class, $documentFile, $service]);

                // Initialize filepond file
                $filepond = new FilepondFile($fileCode, true);

                // Get temporary file name
                $uploadFilename = $filepond->getFileName();

                // Handle non-existing temp upload file
                if (empty($uploadFilename)) {
                    throw new NotFoundHttpException('Missing upload file.');
                }

                // Use filename of temp upload
                $documentFile->document_name = $uploadFilename;

                if ($documentFile->save()) {
                    // Generate filename
                    $targetFilename = $documentFile->id . '_' . $uploadFilename;

                    // Generate target directory
                    switch ($service->type) {
                        case ServiceSelectionTypes::RIO:
                            $targetDirectory = config('bphero.rio_document_storage_path') . $service->data->id;
                            break;
                        case ServiceSelectionTypes::NEO:
                            $targetDirectory = config('bphero.neo_document_storage_path') . $service->data->id;
                            break;
                        default:
                            $targetDirectory = null;
                            break;
                    }

                    // Handle invalid service type session
                    if (empty($targetDirectory)) {
                        throw new ServiceSessionNotFoundException();
                    }

                    // Transfer temporary file to permanent directory
                    $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

                    // Update storage path
                    $documentFile->update([
                        'mime_type' => $fileinfo['mime_type'],
                        'file_bytes' => $fileinfo['file_size'],
                        'storage_path' => config('bphero.private_directory') . '/'
                            . $targetDirectory . '/'
                            . $targetFilename,
                    ]);

                    // Prepare updated storage info
                    $available = $storageInfo['available'] - $fileinfo['file_size'];
                    $storageInfo['used'] = $storageInfo['used'] + $fileinfo['file_size'];
                    $storageInfo['available'] = $available < 0 ? 0 : $available;
                }
            }

            // Update service settings record
            $serviceSetting->data = json_encode($storageInfo) ?: null;
            $serviceSetting->save();

            DB::commit();

            return response()->respondSuccess();
        } catch (NotFoundHttpException $e) {
            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->respondInternalServerError($e);
        }
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
     * Retry chunk offset upload
     *
     * @param \App\Http\Requests\Document\RetryChunkRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function retryChunk(RetryChunkRequest $request)
    {
        $requestData = $request->validated();

        // Initialize filepond file
        $filepond = new FilepondFile($requestData['patch'], true);

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
     * Setup stream wrapper for s3 adapter
     *
     * @param string $bucket
     * @return void
     */
    public function s3Adapter($bucket)
    {
        if (Storage::disk($bucket)->isS3Adapter($bucket)) {
            Storage::disk($bucket)->initializeS3Stream($bucket);
        }

        return;
    }

    /**
     * Retrieves all of the storage path of affected files when parent folder is deleted
     *
     * @param int $id
     * @return iterable
     */
    private function getFilesToDelete($id): iterable
    {
        /** @var array */
        $documentChildFiles = Document::withTrashed()->where('documents.directory_id', $id)->get();

        if (!empty($documentChildFiles)) {
            foreach ($documentChildFiles as $file) {
                switch ($file->document_type) {
                    case DocumentTypes::FOLDER:
                        yield from $this->getFilesToDelete($file->id);
                        break;

                    case DocumentTypes::FILE:
                        //Get the valid s3 bucket storage path
                        $storagePath = explode("/", $file->storage_path);
                        $storagePath = array_splice($storagePath, 1, 4);
                        $storagePath = implode("/", $storagePath);

                        yield $storagePath;
                        break;

                    default:
                        break;
                }
            }
        }
    }

    /**
     * Display documentation list.
     *
     * @param \App\Http\Requests\Document\DocumentRequest $request
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function download(DocumentRequest $request, $id)
    {
        // Get validated data
        $requestData = $request->validated();

        // Setup private s3 bucket
        self::s3Adapter(config('bphero.private_bucket'));

        /** @var User */
        $user = auth()->user();
        $service = json_decode(Session::get('ServiceSelected'));

        /** check referer if from chat */
        $referer = (string) request()->headers->get('referer');
        if (Str::contains($referer, 'messages')) {
            $service = json_decode(Session::get('talkSubject'));
        }

        /** @var Document */
        $document = Document::whereId($id)->first();
        $timeNow = Carbon::now()->format('YmdHis');

        // Check authorization
        $this->authorize('download', [Document::class, $document, $service]);

        //Delete zip files that are 1 or more days old
        $oldFiles = Storage::disk('public')->allFiles(config('bphero.temp_download_directory'));
        foreach ($oldFiles as $oldFile) {
            $fileInfo = explode('_', $oldFile);
            $fileDateInfo = str_replace(config('bphero.temp_download_directory'), '', $fileInfo[0]);
            $fileDate = Carbon::parse($fileDateInfo)->addDays(1)->format('YmdHis');
            if ($fileDate <= $timeNow) {
                Storage::disk('public')->delete($oldFile);
            }
        }

        // Generate download filename
        $fileName = $document->isFolder()
            ? $document->document_name . '.zip'
            : $document->document_name;

        // Generate download content type
        $contentType = $document->isFolder()
            ? 'application/zip'
            : $document->mime_type;

        // Prepare headers
        $headers = [
            'Content-Type'        => 'Content-Type: ' . $contentType,
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        // Prepare storage path
        $source_path = str_replace('private/', '', $document->storage_path);

        // Download single files
        if (!empty($document->mime_type)) {
            if (Storage::disk(config('bphero.private_bucket'))->exists($source_path)) {
                return Download::make(Storage::disk(config('bphero.private_bucket'))->get($source_path), 200, $headers);
            }

            return response()->respondNotFound();
        }

        // Prepare file directories for file compression (.zip)
        Storage::disk('public')->makeDirectory(config('bphero.temp_download_directory'));
        $publicPath = $timeNow . '_' . $user->id . '_' . $document->id . '_' . $fileName;
        $temporaryPath = $user->id . '_' . $document->id . '_' . $fileName;
        $files = $this->getFiles($id, $document->document_name, []);
        $localPath = Storage::disk('public')->path($temporaryPath);

        // Exit if folder(s) are empty
        if (empty($files)) {
            return response()->respondSuccess([], __('The folder is empty'));
        }

        // Folder compression into a local zip file
        $zip = new Filesystem(new ZipArchiveAdapter($localPath));
        foreach ($files as $file) {
            if (!empty($file)) {
                if (Storage::disk(config('bphero.private_bucket'))->exists($file['s3Path'])) {
                    $fileContent = Storage::disk(config('bphero.private_bucket'))->get($file['s3Path']);
                    $zip->put($file['zipPath'], $fileContent);
                }
            }
        }
        $zip->getAdapter()->getArchive()->close();

        // Check if file already exists in temp/download/ directory, if exists ? delete file : move file
        if (Storage::disk('public')->exists(config('bphero.temp_download_directory') . $publicPath)) {
            Storage::disk('public')->delete(config('bphero.temp_download_directory') . $publicPath);
        }
        Storage::disk('public')->move($temporaryPath, config('bphero.temp_download_directory') . $publicPath);

        // Download zip file
        return Download::make(Storage::disk('public')->get(config('bphero.temp_download_directory') . $publicPath), 200, $headers);
    }

    /**
     * Build the file path for each items within the folder
     *
     * @param int $id
     * @param string $folderName
     * @param array $directories
     *
     * @return array $directories
     */
    public function getFiles($id, $folderName, $directories)
    {
        $documents = Document::whereDirectoryId($id)->get();

        if (!empty($documents)) {
            foreach ($documents as $document) {
                switch ($document->document_type) {
                    case (DocumentTypes::FILE):
                        $filePath = str_replace('private/', '', $document->storage_path);
                        array_push($directories, ['zipPath' => $folderName . '/' . $document->document_name, 's3Path' => $filePath]);
                        break;
                    case (DocumentTypes::FOLDER):
                        $directories = $this->getFiles($document->id, $folderName . '/' . $document->document_name, $directories);
                        break;
                    default:
                        break;
                }
            }
        }

        return $directories;
    }

    /**
     * Fetch list of connected rio, neo, and neo groups with share access.
     *
     * @param \App\Http\Requests\Document\DocumentPermittedListRequest $request
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function permittedList(DocumentPermittedListRequest $request, $id)
    {
        /** @var User */
        $user = auth()->user();
        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                'data' => $user->rio,
                'type' => ServiceSelectionTypes::RIO
            ]));
        }
        $service = json_decode(Session::get('ServiceSelected'));

        /** @var Document */
        $document = Document::whereId($id)->first();

        // Guard clause if non-existing document
        if (empty($document)) {
            return response()->respondNotFound();
        }

        // Check authorization
        $this->authorize('viewPermittedList', [Document::class, $document, $service]);

        $permittedList = DocumentAccess::permittedList($id)
            ->paginate(config('bphero.paginate_count'));

        return DocumentPermittedListResource::collection($permittedList)
            ->additional(['meta' => [
                'permitted_list' => '',
            ]]);
    }

    /**
     * Remove share access to selected service.
     *
     * @param \App\Http\Requests\Document\DocumentAccessRequest $request
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function unshare(DocumentAccessRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            /** @var User */
            $user = auth()->user();
            if (empty(json_decode(Session::get('ServiceSelected')))) {
                Session::put('ServiceSelected', json_encode([
                    'data' => $user->rio,
                    'type' => ServiceSelectionTypes::RIO
                ]));
            }
            $service = json_decode(Session::get('ServiceSelected'));

            /** @var Document */
            $documentAccess = DocumentAccess::whereId($id)->first();

            // Guard clause if non-existing document
            if (empty($documentAccess)) {
                return response()->respondNotFound();
            }

            // Check authorization
            $this->authorize('unshare', [DocumentAccess::class, $documentAccess, $service]);

            // Delete document
            $documentAccess->delete();

            DB::commit();

            return response()->respondSuccess(null, __('Successfully removed share permission'));
        } catch (NotFoundHttpException $e) {
            DB::rollBack();

            return response()->respondNotFound();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Fetch list of connected rio, neo, and neo groups without share access.
     *
     * @param \App\Http\Requests\Document\DocumentSearchShareSettingRequest $request
     * @param int $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function connectedList(DocumentSearchShareSettingRequest $request, $id)
    {
        // Get request data
        $requestData = $request->validated();

        /** @var User */
        $user = auth()->user();
        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                'data' => $user->rio,
                'type' => ServiceSelectionTypes::RIO
            ]));
        }
        $service = json_decode(Session::get('ServiceSelected'));

        $result = RioConnection::connectedNoAccessList($service, $requestData)
            ->paginate(config('bphero.paginate_count'));

        return DocumentConnectedListResource::collection($result)
            ->additional(['meta' => [
                'search_key' => $requestData['search'] ?? null,
            ]]);
    }

    /**
     * deletes a file in s3 bucket
     *
     * @param string $storagePath
     * @return void
     */
    private function deleteS3File($storagePath)
    {
        $disk = Storage::disk(config('bphero.private_bucket'));
        //Update filename in S3 Bucket
        if ($disk->exists($storagePath)) {
            $disk->delete($storagePath);
        }
    }

    /**
     * Returns bool value after checking folder content
     *
     * @param int $id document_id
     * @return bool
     */
    public function checkContent($id)
    {
        try {
            /** @var Document */
            $document = Document::whereId($id)->first();

            // Guard clause if non-existing document
            if (empty($document)) {
                return response()->respondNotFound();
            }

            return !Document::isEmptyDirectory($id);
        } catch (NotFoundHttpException $e) {
            return response()->respondNotFound();
        } catch (\Exception $e) {
            return response()->respondInternalServerError($e);
        }
    }
}
