<?php

namespace App\Http\Controllers\Classified;

use App\Enums\ServiceSelectionTypes;
use App\Events\Classified\ReceiveMessages;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classified\ProcessChunkRequest;
use App\Http\Requests\Classified\ProcessUploadRequest;
use App\Http\Requests\Classified\RevertUploadRequest;
use App\Http\Requests\Classified\SendMessageRequest;
use App\Http\Resources\Classified\MessageResource;
use App\Models\ClassifiedContact;
use App\Models\ClassifiedContactMessage;
use App\Models\ClassifiedSetting;
use App\Objects\FilepondFile;
use App\Models\Neo;
use App\Models\Rio;
use App\Objects\ServiceSelected;
use DB;
use Illuminate\Http\UploadedFile;

class ContactMessageController extends Controller
{
    /**
     * Contact messages conversation page.
     *
     * @param \App\Models\ClassifiedContact $contact
     * @return \Illuminate\View\View
     */
    public function index(ClassifiedContact $contact)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Guard clause for non-accessible inquiry conversation
        $this->authorize('access', [ClassifiedContact::class, $contact]);

        // Get selectable payment settings and bank accounts
        $settings = ClassifiedSetting::getSelectableSettings();
        $bankAccounts = ClassifiedSetting::getBankAccounts();

        // Get receiver information
        $receiver = $contact->getReceiver();

        // Check if service is the buyer and get product
        $isBuyer = $contact->isBuyer();
        $product = $contact->classified_sale;

        return view('classifieds.contact-messages.index', compact(
            'rio',
            'service',
            'contact',
            'receiver',
            'isBuyer',
            'product',
            'settings',
            'bankAccounts',
        ));
    }

    /**
     * Get contact inquiry messages.
     *
     * @param int $id Contact ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages(int $id)
    {
        // Get rio of authenticated user
        /** @var \App\Models\User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get contact
        $contact = ClassifiedContact::whereId($id)->firstOrFail();

        // Guard clause for non-accessible inquiry conversation
        if (!$contact->isAllowedAccess()) {
            return response()->respondNotFound();
        }

        // Get inquiry messages
        $messages = ClassifiedContactMessage::messageList($id)->get();

        // Get messages as json resource
        $messages = MessageResource::collection($messages);

        return response()->respondSuccess($messages);
    }

    /**
     * Send inquiry message.
     *
     * @param \App\Http\Requests\Classified\SendMessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(SendMessageRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        // Get rio of authenticated user
        /** @var \App\Models\User */
        $user = auth()->user();
        $rio = $user->rio;

        // Guard clause for non-existing rio
        if (empty($rio)) {
            return response()->respondNotFound();
        }

        // Get contact
        $contact = ClassifiedContact::whereId($requestData['classified_contact_id'])->firstOrFail();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Guard clause for non-accessible inquiry conversation
        if (!$contact->isAllowedAccess()) {
            return response()->respondNotFound();
        }

        // Set disk as Public bucket
        $disk = config('bphero.public_bucket');

        DB::beginTransaction();

        try {
            // Create new contact message
            $contactMessage = new ClassifiedContactMessage();
            $contactMessage->fill($requestData);

            // Transfer temp images to permanent directory
            $uploadedImages = $requestData['upload_file'] ?? [];
            $attaches = [];

            if (!empty($uploadedImages)) {
                foreach ($uploadedImages as $image) {
                    // Initialize filepond file
                    $filepond = new FilepondFile($image, true, $disk);

                    // Get temporary file name
                    $uploadFilename = $filepond->getFileName();

                    // Handle non-existing temp upload file
                    if (empty($uploadFilename)) {
                        continue;
                    }

                    // Set target directory and filename
                    $targetDirectory = config('bphero.messages_storage_path') . $requestData['classified_contact_id'];
                    $targetFilename = date('YmdHis') . '_' . $uploadFilename;

                    // Transfer temporary file to permanent directory
                    $filepond->transferFile($targetDirectory, $targetFilename, false);

                    // Store in image path array
                    $attaches[] = config('bphero.public_directory') . '/' . $targetDirectory . '/' . $targetFilename;
                }
            }

            // Save contact message
            if ($contactMessage->save()) {
                // Update attachments
                if (!empty($attaches)) {
                    $contactMessage->update([
                        'attaches' => json_encode($attaches, JSON_FORCE_OBJECT) ?? null,
                    ]);
                }
            }

            // Send notification to receiver when a new message is sent
            ClassifiedContact::sendNotification($contact, $requestData['sender']);

            // Commit database changes
            DB::commit();

            //Mail notification
            $sender = null;
            $receiver = null;

            //receiver should be buyer
            if ($contactMessage->sender === 0) {
                $receiver = Neo::whereId($contact->neo_id)->first();
                if (!empty($contact->rio_id)) {
                    $receiver = Rio::whereId($contact->rio_id)->first();
                }
            }

            //receiver should be seller
            if ($contactMessage->sender === 1) {
                $receiver = Neo::whereId($contact->selling_neo_id)->first();
                if (!empty($contact->selling_rio_id)) {
                    $receiver = Rio::whereId($contact->selling_rio_id)->first();
                }
            }

            //Get sender based on service type
            if ($service->type === ServiceSelectionTypes::RIO) {
                $sender =  Rio::whereId($service->data->id)->first();
            }

            if ($service->type === ServiceSelectionTypes::NEO) {
                $sender =  Neo::whereId($service->data->id)->first();
            }

            $contactMessage->sendNetshopChatEmail($sender, $receiver, $contactMessage);

            // Get inquiry messages
            $contactMessage = ClassifiedContactMessage::messageList($requestData['classified_contact_id'])
                ->find($contactMessage->id);

            // Get messages as json resource
            $contactMessage = MessageResource::collection([$contactMessage]);

            // Broadcast message to channels
            ReceiveMessages::dispatch($requestData['classified_contact_id']);

            return response()->respondSuccess($contactMessage);
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);

            return response()->respondInternalServerError([$exception->getMessage()]);
        }
    }

    /**
     * Process Upload API Endpoint
     *
     * @param \App\Http\Requests\Classified\ProcessUploadRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function processUpload(ProcessUploadRequest $request)
    {
        // Get request data
        $requestData = $request->validated();
        $files = $requestData['upload_file'];

        // Set disk as Public bucket
        $disk = config('bphero.public_bucket');

        // Check if data sent is a file
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $result = FilepondFile::storeTemporaryFile($file, $disk);

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
                $path = FilepondFile::generateTemporaryDirectory($disk);
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
     * @param \App\Http\Requests\Classified\ProcessChunkRequest $request
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function chunkUpload(ProcessChunkRequest $request)
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
     * @param \App\Http\Requests\Classified\RevertUploadRequest $request
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
