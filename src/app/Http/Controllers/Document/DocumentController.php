<?php

namespace App\Http\Controllers\Document;

use App\Enums\Document\DocumentTypes;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Document\DocumentListRequest;
use App\Http\Requests\Document\SharedLinkRequest;
use App\Models\Document;
use App\Models\DocumentAccess;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Enums\NeoBelongStatuses;
use Session;

class DocumentController extends Controller
{
    /**
     * Display documentation list.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function documentList(DocumentListRequest $request)
    {
        // Get request data
        $requestData = $request->validated() ?: null;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));

        return view('documents.lists.document-list', compact(
            'service',
            'requestData',
        ));
    }

    /**
     * Display documentation list.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function folderFileList(DocumentListRequest $request, $id)
    {
        // Get request data
        $requestData = $request->validated();
        $requestData['directory_id'] = $id;
        $directoryId = $id;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $folderDetails = Document::where('documents.id', $directoryId)->first();

        $this->verifyDocument($service, $folderDetails);

        return view('documents.lists.document-folder-file-list', compact(
            'service',
            'directoryId',
            'folderDetails',
            'requestData',
        ));
    }

    /**
     * Display shared documents list.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function sharedDocumentList(DocumentListRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $requestData['for_shared'] = true;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));

        return view('documents.shared_lists.shared-document-list', compact(
            'service',
            'requestData',
        ));
    }

    /**
     * Display shared documents list.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function sharedFolderFileList(DocumentListRequest $request, $id)
    {
        // Get request data
        $requestData = $request->validated();
        $directoryId = $id;
        $requestData['for_shared'] = true;
        $isShared = false;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $folderDetails = Document::where('documents.id', $directoryId)->first();

        if (is_null($folderDetails)) {
            $this->verifyDocument($service, $folderDetails);
        }

        //Determine if current viewer is RIO or NEO
        $isUserRio = ($service->type === ServiceSelectionTypes::RIO) ? true : false;

        //Determines if the user owns the target folder
        $isOwner = Document::whereId($directoryId)
            ->when($isUserRio, function ($q) use ($service) { // true
                return $q->whereOwnerRioId($service->data->id);
            }, function ($q) use ($service) { // false
                return $q->whereOwnerNeoId($service->data->id);
            })
            ->exists();

        if (!$isOwner) {
            // Checks if the current user has access to the target shared folder
            $isShared = $this->verifySharedSubDirectory($isUserRio, $service, $directoryId);
        }

        $this->verifyDocument($service, $folderDetails, $isShared);

        if ($isShared || isset($requestData['for_shared'])) {
            $requestData['owner_rio_id'] = $folderDetails->owner_rio_id ?? null;
            $requestData['owner_neo_id'] = $folderDetails->owner_neo_id ?? null;
            unset($requestData['for_shared']);
        }

        return view('documents.shared_lists.shared-document-folder-file-list', compact(
            'service',
            'directoryId',
            'folderDetails',
            'requestData',
        ));
    }

    /**
     * Display personal file.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function filePreview(DocumentListRequest $request, $id)
    {
        // Get request data
        $requestData = $request->validated();
        $fileId = $id;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $fileDetails = Document::where('documents.id', $fileId)->first();

        if (is_null($fileDetails)) {
            $this->verifyDocument($service, $fileDetails);
        }

        $this->verifyDocument($service, $fileDetails);

        return view('documents.previews.document-file-preview', compact(
            'service',
            'fileId',
            'fileDetails',
            'requestData',
        ));
    }

    /**
     * Display shared file.
     *
     * @param \App\Http\Requests\Document\DocumentListRequest $request
     * @param int $id
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function sharedFilePreview(DocumentListRequest $request, $id)
    {
        // Get request data
        $requestData = $request->validated();
        $fileId = $id;
        $isShared = false;
        $isViewable = false;
        $requestData['shared_folder'] = true;

        /** @var User */
        $user = auth()->user();

        if (empty(json_decode(Session::get('ServiceSelected')))) {
            Session::put('ServiceSelected', json_encode([
                "data" => $user->rio,
                "type" => ServiceSelectionTypes::RIO
            ]));
        }

        $service = json_decode(Session::get('ServiceSelected'));
        $fileDetails = Document::where('documents.id', $fileId)->first();

        if (is_null($fileDetails)) {
            $this->verifyDocument($service, $fileDetails);
        }

        //Determine if current viewer is RIO or NEO
        $isUserRio = ($service->type === ServiceSelectionTypes::RIO) ? true : false;

        //Determines if the user owns the target file
        $isOwner = Document::whereId($fileId)
            ->when($isUserRio, function ($q) use ($service) { // true
                return $q->whereOwnerRioId($service->data->id);
            }, function ($q) use ($service) { // false
                return $q->whereOwnerNeoId($service->data->id);
            })
            ->exists();

        if (!$isOwner) {
            $requestData['for_shared'] = true;
            // Checks if the current user has access to the target shared file
            $isViewable = DocumentAccess::whereDocumentId($fileId)
                ->when($isUserRio, function ($q) use ($service) { // true
                    return $q->whereRioId($service->data->id);
                }, function ($q) use ($service) { // false
                    return $q->whereNeoId($service->data->id);
                })
                ->exists();
            // Checks if the current user has access to the target shared file within the shared folder
            $isShared = $this->verifySharedSubDirectory($isUserRio, $service, $fileDetails->directory_id);
        }

        $this->verifyDocument($service, $fileDetails, $isShared, $isViewable);

        if ($isViewable || $isShared || isset($requestData['for_shared'])) {
            $requestData['owner_rio_id'] = $fileDetails->owner_rio_id ?? null;
            $requestData['owner_neo_id'] = $fileDetails->owner_neo_id ?? null;
            unset($requestData['for_shared']);
        }

        return view('documents.previews.document-file-preview', compact(
            'service',
            'fileId',
            'fileDetails',
            'requestData',
        ));
    }

    /**
     * Checks if the subfolder's parent folder is shared to the accessor
     *
     * @param bool $isUserRio
     * @param object $service
     * @param int|null $directoryId
     *
     * @return void|bool
     */
    private function verifySharedSubDirectory($isUserRio, $service, $directoryId = null)
    {
        $isShared = Document::join('document_accesses', 'documents.id', '=', 'document_accesses.document_id')
            ->where('document_accesses.document_id', $directoryId)
            ->when($isUserRio, function ($q) use ($service) { // true
                return $q->where('document_accesses.rio_id', $service->data->id);
            }, function ($q) use ($service) { // false
                return $q->where('document_accesses.neo_id', $service->data->id);
            })
            ->exists();

        if (!$isShared) {
            if (!is_null($directoryId)) {
                /** @var Document */
                $folderDetails = Document::where('documents.id', $directoryId)->first();

                return $this->verifySharedSubDirectory($isUserRio, $service, $folderDetails->directory_id);
            }

            return false;
        }

        return true;
    }

    /**
     * Validate selected/accessed file/folder
     *
     * @param object $service
     * @param \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Query\Builder|null $documentDetails
     * @param bool|void $isShared
     * @param bool|void $isViewable
     *
     * @return void|null
     */
    private function verifyDocument($service, $documentDetails, $isShared = false, $isViewable = false)
    {
        if (is_null($documentDetails)) {
            abort(404);
        }

        if ($documentDetails->document_type === DocumentTypes::FOLDER) {
            if (!is_null($documentDetails->mime_type) && !is_null($documentDetails->file_bytes)) {
                abort(422);
            }
        }

        if (!$isShared && !$isViewable) {
            if (!in_array($service->data->id, [$documentDetails->owner_rio_id, $documentDetails->owner_neo_id])) {
                abort(404);
            }

            if ((!is_null($documentDetails->owner_rio_id) && ($service->type != ServiceSelectionTypes::RIO))
            || (!is_null($documentDetails->owner_neo_id) && ($service->type != ServiceSelectionTypes::NEO))) {
                abort(401);
            }
        }
    }

    /**
     * Create file.
     * To be deleted after testing.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function createFile()
    {
        // Setup stream wrapper for s3 adapter
        self::s3Adapter(config('bphero.public_bucket'));
        self::s3Adapter(config('bphero.private_bucket'));

        if (!Storage::disk(config('bphero.public_bucket'))->exists('public.txt')) {
            Storage::disk(config('bphero.public_bucket'))->put('public.txt', 'this is public');
        }

        if (!Storage::disk(config('bphero.private_bucket'))->exists('private.txt')) {
            Storage::disk(config('bphero.private_bucket'))->put('private.txt', 'this is private');
        }

        return view('documents.minio_tests.create-file');
    }

    /**
     * Update file.
     * To be deleted after testing.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function updateFile()
    {
        // Setup stream wrapper for s3 adapter
        self::s3Adapter(config('bphero.public_bucket'));
        self::s3Adapter(config('bphero.private_bucket'));

        if (Storage::disk(config('bphero.public_bucket'))->exists('public.txt')) {
            Storage::disk(config('bphero.public_bucket'))->append('public.txt', 'THIS IS STILL PUBLIC');
        }

        if (Storage::disk(config('bphero.private_bucket'))->exists('private.txt')) {
            Storage::disk(config('bphero.private_bucket'))->append('private.txt', 'THIS IS STILL PRIVATE');
        }

        return view('documents.minio_tests.update-file');
    }

    /**
     * Delete file.
     * To be deleted after testing.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function deleteFile()
    {
        // Setup stream wrapper for s3 adapter
        self::s3Adapter(config('bphero.public_bucket'));
        self::s3Adapter(config('bphero.private_bucket'));

        if (Storage::disk(config('bphero.public_bucket'))->exists('public.txt')) {
            Storage::disk(config('bphero.public_bucket'))->delete('public.txt');
        }

        if (Storage::disk(config('bphero.private_bucket'))->exists('private.txt')) {
            Storage::disk(config('bphero.private_bucket'))->delete('private.txt');
        }

        return view('documents.minio_tests.delete-file');
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
     * Check services affiliated to current logged RIO given share access
     *
     * @param string|int $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function sharedLinkAccessCheck($id)
    {
        /** @var User */
        $user = auth()->user();
        $currentUser = $user->rio;
        $serviceSession = json_decode(Session::get('ServiceSelected'));
        $neosFiltered = [];

        // Check if document exists
        $document = Document::where('id', $id)->firstOrFail();

        // Fetch neos given access to document
        $documentAccess = DocumentAccess::whereDocumentId($id)
            ->whereNotNull('neo_id')
            ->whereNull('deleted_at')
            ->pluck('neo_id');

        // Fetch affiliated neos with share access
        $neos = $user->rio->neos()
            ->whereStatus(NeoBelongStatuses::AFFILIATED)
            ->paginate(config('bphero.paginate_count'));

        // Fetch affiliated neos with share access
        $neosFilteredQuery = $user->rio->neos()
            ->whereStatus(NeoBelongStatuses::AFFILIATED)
            ->whereIn('neo_id', $documentAccess);

        if ($neosFilteredQuery->count() !== 0) {
            $neosFiltered = $neosFilteredQuery->get();
        }

        // Check if RIO has share access
        $isRioAllowed = DocumentAccess::whereDocumentId($id)
            ->whereRioId($user->rio->id)
            ->whereNull('deleted_at')
            ->exists();

        return view('home', compact('serviceSession', 'neos', 'currentUser', 'document', 'neosFiltered', 'isRioAllowed'));
    }

    /**
     * Change service selected and redirect to folder/file
     *
     * @param SharedLinkRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function sharedLinkAccess(SharedLinkRequest $request)
    {
        /** @var User */
        $user = auth()->user();
        $requestData = $request->validated();
        $accessRoute = '';

        // Check if document exists
        $document = Document::where('id', $requestData['document_id'])->first();

        // Guard clause if non-existing document
        if (empty($document)) {
            return response()->respondNotFound();
        }

        // Determine redirect route
        switch ($document->document_type) {
            case (DocumentTypes::FOLDER):
                $accessRoute =  '/document/shared/folders/:id';
                break;
            case (DocumentTypes::FILE):
                $isPreview = explode('/', $document->mime_type);
                $accessRoute =  '/document/shared/files/:id';
                if (!(in_array('image', $isPreview) || in_array('pdf', $isPreview))) {
                    $accessRoute =  '/api/document/download/:id';
                }
                break;
            default:
                break;
        }

        // Set service selected NEO
        switch ($requestData['service']) {
            case (ServiceSelectionTypes::RIO):
                $rio =  $user->rio;
                Session::put('ServiceSelected', json_encode([
                    "data" => $rio,
                    "type" => ServiceSelectionTypes::RIO
                ]));
                break;
            case (ServiceSelectionTypes::NEO):
                $neoBelong =  $user->rio->neos->where('id', $requestData['neo_id'])->first();
                Session::put('ServiceSelected', json_encode([
                    "data" => $neoBelong,
                    "type" => ServiceSelectionTypes::NEO
                ]));
                break;
            default:
                break;
        }

        return response()->respondSuccess($accessRoute);
    }
}
