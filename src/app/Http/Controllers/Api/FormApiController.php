<?php

namespace App\Http\Controllers\Api;

use App\Enums\Connection\ListFilters;
use App\Enums\Form\OperationTypes;
use App\Enums\Form\Types;
use App\Enums\ServiceSelectionTypes;
use App\Exceptions\ServiceSessionNotFoundException;
use App\Helpers\OperationDetailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Form\QuotationProductRequest;
use App\Http\Requests\Form\CreateQuotationRequest;
use App\Http\Requests\Form\CreateReceiptRequest;
use App\Http\Resources\Form\ConnectionListResource;
use App\Models\Form;
use App\Models\FormBasicSetting;
use App\Models\FormHistory;
use App\Models\Neo;
use App\Models\Notification;
use App\Models\Rio;
use App\Models\RioConnection;
use App\Objects\FilepondFile;
use App\Objects\ServiceSelected;
use App\Traits\FilePondUploadable;
use Carbon\Carbon;
use Session;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FormApiController extends Controller
{
    use FilePondUploadable;

    /**
     * Display Connection list.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function connectionList(Request $request)
    {
        // Get request data
        $requestData = $request->all();
        $requestData['mode'] = $requestData['mode'] ?? ListFilters::SHOW_ALL;

        $service = json_decode(Session::get('ServiceSelected'));

        //Get connected list
        $result = RioConnection::connectedList($service, $requestData)
            ->paginate(config('bphero.paginate_count'))
            ->withQueryString();

        return ConnectionListResource::collection($result);
    }

    /**
     * Validate quotation product
     *
     * @param QuotationProductRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function validateProduct(QuotationProductRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        return response()->json($requestData);
    }

    /**
     * Create quotation
     *
     * @param CreateQuotationRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function createQuotation(CreateQuotationRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        if ($requestData['mode'] == 'validation') {
            return response()->respondSuccess($requestData);
        }

        DB::beginTransaction();

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $senderName = null;
        $recipientConnection = null;

        try {
            // Initialize notification
            $notification = [];

            $form = new Form();
            $form->fill($request->formAttributes());

            $formHistory = new FormHistory();
            $formHistory->fill($request->formAttributes());

            if ($service->type === ServiceSelectionTypes::NEO) {
                $form->neo_id = $service->data->id;
                $senderName = $service->data->organization_name;
            } else {
                $form->rio_id = $user->rio_id;
                $senderName = $service->data->full_name . 'さん';
            }

            if (
                isset($requestData['is_supplier_connected']) &&
                !is_null($requestData['is_supplier_connected']) &&
                ($requestData['is_supplier_connected'] === true)
            ) {
                if ($requestData['supplier']['service'] === ServiceSelectionTypes::RIO) {
                    $form->supplier_rio_id = $requestData['supplier']['id'];
                    $formHistory->supplier_rio_id = $requestData['supplier']['id'];
                    $recipientConnection = Rio::whereId($requestData['supplier']['id'])->first();

                    // Set RIO notification receiver
                    $notification += [
                        'rio_id' => $requestData['supplier']['id'],
                        'notification_content' => __('Notification Content - Form Recipient', [
                            'sender_name' => $senderName,
                            'form_type' => __('Quotation2'),
                        ]),
                    ];
                } else {
                    $form->supplier_neo_id = $requestData['supplier']['id'];
                    $formHistory->supplier_neo_id = $requestData['supplier']['id'];

                    /** @var Neo */
                    $neoReceiver = Neo::whereId($requestData['supplier']['id'])->first();
                    $recipientConnection = $neoReceiver;
                    /** @var NeoBelong @phpstan-ignore-next-line */
                    $neoOwner = $neoReceiver->owner;

                    // Set RIO notification receiver based on NEO owner
                    $notification += [
                        /** @phpstan-ignore-next-line */
                        'rio_id' => $neoOwner->rio->id ?? null,
                        'receive_neo_id' => $requestData['supplier']['id'],
                        'notification_content' => __('Notification Content - Form Recipient', [
                            'sender_name' => $senderName,
                            'form_type' => __('Quotation2'),
                        ]),
                    ];
                }
                $form->supplier_name = $requestData['supplier_name'];
                $formHistory->supplier_name = $requestData['supplier_name'];
            } else {
                unset($requestData['supplier']);
                $form->supplier_name = $requestData['supplier_name'];
                $formHistory->supplier_name = $requestData['supplier_name'];
            }

            $form->created_rio_id = $user->rio_id;
            $form->save();

            // Save form history
            $formHistory->operation_datetime = Carbon::now();
            $formHistory->operation_details = OperationDetailHelper::getRegistrationMessage($form->title);
            $formHistory->operator_email = $user->email;
            $formHistory->form_id = $form->id;
            $formHistory->created_rio_id = $user->rio_id;
            $formHistory->save();

            if (isset($requestData['logo']) && $requestData['logo']) {
                $fileCode = $requestData['logo'];
                // Initialize filepond file
                $filepond = new FilepondFile($fileCode, true, config('bphero.public_bucket'));

                // Get temporary file name
                $uploadFilename = $filepond->getFileName();

                // Handle non-existing temp upload file
                if (empty($uploadFilename)) {
                    throw new NotFoundHttpException('Missing upload file.');
                }

                $targetFilename = $form->id . '_' . $uploadFilename;

                // Generate target directory
                $targetDirectory = config('bphero.form_issuer_image');

                // Handle invalid service type session
                if (empty($targetDirectory)) {
                    throw new ServiceSessionNotFoundException();
                }

                $targetFilename = $form->id . '_' . $uploadFilename;

                // Transfer temporary file to permanent directory
                $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

                $form->issuer_image = $targetFilename;
                $form->save();

                $formHistory->issuer_image = $formHistory::copyToHistory($formHistory, $filepond, $targetFilename);
                $formHistory->save();
            } else {
                if ($requestData['mode'] !== OperationTypes::DUPLICATE) {
                    $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

                    if (empty($formBasicSetting)) {
                        $formBasicSetting = FormBasicSetting::getSettingFromRioNeo($service);
                    }

                    if ($formBasicSetting && $formBasicSetting->image) {
                        $form->issuer_image = $formBasicSetting->image;
                        $form->save();

                        $formHistory->issuer_image = $formBasicSetting->image;
                        $formHistory->save();
                    }
                }

                $form->issuer_image = $form::duplicateImage($form);
                $form->save();

                $formHistory->issuer_image = $form::duplicateImage($form, $formHistory);
                $formHistory->save();
            }

            if (!empty($requestData['products'])) {
                $products = [];
                foreach ($requestData['products'] as $product) {
                    $product['created_rio_id'] = $user->rio_id;
                    if ($service->type === ServiceSelectionTypes::RIO) {
                        $product['rio_id'] = $user->rio_id;
                    }
                    if ($service->type === ServiceSelectionTypes::NEO) {
                        $product['neo_id'] = $service->data->id;
                    }
                    array_push($products, $product);
                }
                $form->products()->createMany($products);
                $formHistory->products()->createMany($products);
            }

            // Send notification to form supplier
            if (!empty($notification) && $notification['rio_id'] !== null) {
                Notification::createNotification($notification);
            }

            DB::commit();

            if ($requestData['is_supplier_connected']) {
                $form->sendEmailToConnectionRecipient($senderName, $recipientConnection, $form);
            }

            session()->put('alert', [
                'status' => 'success',
                'message' => __('The quotation has been issued'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            Log::debug($e);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Update quotation
     *
     * @param CreateQuotationRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function updateQuotation(CreateQuotationRequest $request, Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        // Get operation details
        $operationDetails = OperationDetailHelper::getEditMessage($form, $requestData, Types::QUOTATION);

        // Generate target directory
        $targetDirectory = config('bphero.form_issuer_image');
        $storagePath = "{$targetDirectory}/{$form->issuer_image}";

        if (!empty($operationDetails)) {
            $form->supplier_rio_id = null;
            $form->supplier_neo_id = null;
            $form->supplier_name = null;
        }

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $senderName = null;
        $previousRecipientConnection = $form->supplier_rio_id ?? $form->supplier_neo_id;
        $recipientConnection = null;

        try {
            // Initialize notification
            $notification = [];

            if ($service->type === ServiceSelectionTypes::NEO) {
                $senderName = $service->data->organization_name;
            } else {
                $senderName = $service->data->full_name . 'さん';
            }

            $formHistory = new FormHistory();
            $formHistory->fill($request->formAttributes());
            $formHistory->operation_datetime = Carbon::now();
            $formHistory->operator_email = $user->email;
            $formHistory->form_id = $form->id;
            $formHistory->created_rio_id = $user->rio_id;
            $formHistory->issuer_image = $formHistory->getMissingPath($storagePath, $formHistory) ?? null;

            // Update supplier id and name
            if (
                isset($requestData['is_supplier_connected']) &&
                !is_null($requestData['is_supplier_connected']) &&
                ($requestData['is_supplier_connected'] === true)
            ) {
                if ($requestData['supplier']['service'] === ServiceSelectionTypes::RIO) {
                    $form->supplier_rio_id = $requestData['supplier']['id'];
                    $formHistory->supplier_rio_id = $requestData['supplier']['id'];
                    $recipientConnection = Rio::whereId($requestData['supplier']['id'])->first();

                    // Set RIO notification receiver
                    $notification += [
                        'rio_id' => $requestData['supplier']['id'],
                        'notification_content' => __('Notification Content - Form Recipient', [
                            'sender_name' => $senderName,
                            'form_type' => __('Quotation2'),
                        ]),
                    ];
                } else {
                    $form->supplier_neo_id = $requestData['supplier']['id'];
                    $formHistory->supplier_neo_id = $requestData['supplier']['id'];

                    /** @var Neo */
                    $neoReceiver = Neo::whereId($requestData['supplier']['id'])->first();
                    $recipientConnection = $neoReceiver;
                    /** @var NeoBelong @phpstan-ignore-next-line */
                    $neoOwner = $neoReceiver->owner;

                    // Set RIO notification receiver based on NEO owner
                    $notification += [
                        /** @phpstan-ignore-next-line */
                        'rio_id' => $neoOwner->rio->id ?? null,
                        'receive_neo_id' => $requestData['supplier']['id'],
                        'notification_content' => __('Notification Content - Form Recipient', [
                            'sender_name' => $senderName,
                            'form_type' => __('Quotation2'),
                        ]),
                    ];
                }
                $form->supplier_name = $requestData['supplier_name'];
                $formHistory->supplier_name = $requestData['supplier_name'];
            } else {
                unset($requestData['supplier']);
                $form->supplier_name = $requestData['supplier_name'];
                $formHistory->supplier_name = $requestData['supplier_name'];
            }

            if (!empty($operationDetails)) {
                $form->update($request->formAttributes());

                // Save new history
                $formHistory->operation_details = $operationDetails;
                $formHistory->save();

                // Delete all existing products in quotation
                $form->products()->delete();
            }

            if (!empty($requestData['products'])) {
                $products = [];
                foreach ($requestData['products'] as $product) {
                    $product['created_rio_id'] = $user->rio_id;
                    if ($service->type === ServiceSelectionTypes::RIO) {
                        $product['rio_id'] = $user->rio_id;
                    }
                    if ($service->type === ServiceSelectionTypes::NEO) {
                        $product['neo_id'] = $service->data->id;
                    }
                    array_push($products, $product);
                }

                if (!empty($operationDetails)) {
                    $form->products()->createMany($products);
                    $formHistory->products()->createMany($products);
                }
            }

            if (isset($requestData['logo']) && $requestData['logo']) {
                $fileCode = $requestData['logo'];
                // Initialize filepond file
                $filepond = new FilepondFile($fileCode, true, config('bphero.public_bucket'));

                // Get temporary file name
                $uploadFilename = $filepond->getFileName();

                // Handle non-existing temp upload file
                if (empty($uploadFilename)) {
                    throw new NotFoundHttpException('Missing upload file.');
                }

                $targetFilename = $form->id . '_' . $uploadFilename;

                // Handle invalid service type session
                if (empty($targetDirectory)) {
                    throw new ServiceSessionNotFoundException();
                }

                $this->deleteS3File($targetDirectory . '/' . $targetFilename);

                // Transfer temporary file to permanent directory
                $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

                $form->issuer_image = $targetFilename;
                $form->save();

                $formHistory->issuer_image = $formHistory::copyToHistory($formHistory, $filepond, $targetFilename);
                $formHistory->save();
            } else {
                // If image has been deleted, set image to null
                $form->issuer_image = null;
                $form->save();
            }

            // Send notification to form supplier
            if (!empty($notification) && $notification['rio_id'] !== null) {
                Notification::createNotification($notification);
            }

            DB::commit();

            //If supplier changed and is connected send notif
            if ($requestData['is_supplier_connected']) {
                if ($previousRecipientConnection !== $recipientConnection->id) {
                    $form->sendEmailToConnectionRecipient($senderName, $recipientConnection, $form);
                }
            }

            session()->put('alert', [
                'status' => 'success',
                'message' => __('The quotation has been updated'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            Log::debug($e);

            return response()->respondInternalServerError();
        }
    }

    /**
     * deletes a file in s3 bucket
     *
     * @param string $storagePath
     * @return void
     */
    private function deleteS3File($storagePath)
    {
        $disk = Storage::disk(config('bphero.public_bucket'));
        //Update filename in S3 Bucket
        if ($disk->exists($storagePath)) {
            $disk->delete($storagePath);
        }
    }

    /**
     * Create and prepare receipt data
     *
     * @param CreateReceiptRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function createReceipt(CreateReceiptRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        if ($requestData['mode'] == 'validation') {
            return response()->json($requestData);
        }

        return response()->json(['save' => 'save']);
    }
}
