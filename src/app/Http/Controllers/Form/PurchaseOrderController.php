<?php

namespace App\Http\Controllers\Form;

use App\Enums\Form\OperationTypes;
use App\Enums\Form\Types;
use App\Enums\ServiceSelectionTypes;
use App\Exceptions\ServiceSessionNotFoundException;
use App\Helpers\OperationDetailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Form\CreatePurchaseOrderRequest;
use App\Http\Resources\Form\FormResource;
use App\Http\Requests\Form\PurchaseOrderSearchInputRequest;
use App\Models\Form;
use App\Models\FormHistory;
use App\Models\FormBasicSetting;
use App\Models\Neo;
use App\Models\Notification;
use App\Models\Rio;
use App\Objects\FilepondFile;
use App\Objects\FormProductCalculations;
use App\Objects\ServiceSelected;
use App\Traits\FilePondUploadable;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PurchaseOrderController extends Controller
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
     * Create purchasse order view
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        return view('forms.purchase-orders.index', compact(
            'rio',
            'service',
        ));
    }

    /**
     * Purchase order registration page
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

        return view('forms.purchase-orders.create', compact(
            'rio',
            'service',
            'formBasicSetting'
        ));
    }

    /**
     * Form purchase order duplication.
     *
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|void
     */
    public function duplicate(Form $form)
    {
        $this->authorize('createFromDiffForm', [Form::class, $form, Types::PURCHASE_ORDER]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Get form basic settings
        $formBasicSetting = FormBasicSetting::currentEntityServiceSetting()->first();

        // Unset properties that will not be inherited
        unset(
            $form->form_no,
            $form->delivery_date,
            $form->delivery_deadline,
            $form->issue_date,
            $form->payment_date,
            $form->expiration_date,
            $form->receipt_date,
        );

        return view('forms.purchase-orders.duplicate', compact(
            'rio',
            'service',
            'formBasicSetting',
            'form'
        ));
    }

    /**
     * Purchase order edit page
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, Form $form)
    {
        $this->authorize('edit', [Form::class, $form, Types::PURCHASE_ORDER]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

        return view('forms.purchase-orders.edit', compact(
            'rio',
            'form',
            'service',
            'formBasicSetting'
        ));
    }

    /**
     * Display Purchase Order details.
     *
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function show(Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();
        $rio = $user->rio;

        // Get selected service
        $service = ServiceSelected::getSelected();

        // Get ALL necessary form information
        $form = $form
            ->formDetails(Types::PURCHASE_ORDER)
            ->where('forms.id', $form->id)
            ->firstOrFail();

        // Calculate product prices and taxes
        $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

        return view('forms.purchase-orders.show', compact(
            'form',
            'service',
            'pricesAndTaxes'
        ));
    }

    /**
     * Form purchase orders deletion.
     *
     * @param \App\Models\Form $form
     * @param bool $withAlert. Defaults to false.
     * @return mixed
     */
    public function destroy(Form $form, $withAlert = false)
    {
        $this->authorize('delete', [Form::class, $form, Types::PURCHASE_ORDER]);

        /** @var \App\Models\User */
        $user = auth()->user();

        DB::beginTransaction();

        try {
            // Update deleter RIO and delete the form
            $form->update([
                'deleter_rio_id' => $user->rio_id,
            ]);
            $form->delete();

            DB::commit();

            if ($withAlert) {
                session()->put('alert', [
                    'status' => 'success',
                    'message' => __('Form has been deleted'),
                ]);
            }

            return response()->respondSuccess(
                route('forms.purchase-orders.index')
            );
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);

            if ($withAlert) {
                session()->put('alert', [
                    'status' => 'danger',
                    'message' => __('Server Error'),
                ]);
            }

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get purchase order lists
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getPurchaseOrderLists(Request $request)
    {
        $requestData = $request->all();

        $purchaseOrder = Form::formList(Types::PURCHASE_ORDER)
            ->commonConditions($requestData)
            ->paginate(config('bphero.paginate_count'));

        return FormResource::collection($purchaseOrder);
    }

    /**
     * Validate purchase order search
     *
     * @param \App\Http\Requests\Form\PurchaseOrderSearchInputRequest $request
     * @return mixed
     */
    public function validatePurchaseOrderSearch(PurchaseOrderSearchInputRequest $request)
    {
        $requestData = $request->validated();

        return response()->respondSuccess($requestData);
    }

    /**
     * Validate purchase order product
     *
     * @param CreatePurchaseOrderRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function createPurchaseOrder(CreatePurchaseOrderRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        return response()->json($requestData);
    }

    /**
     * Confirm purchase order
     *
     * @param \App\Http\Requests\Form\CreatePurchaseOrderRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function confirm(CreatePurchaseOrderRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();
        $currentDate =  Carbon::now()->format('YmdHis');

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $senderName = null;
        $recipientConnection = null;

        DB::beginTransaction();

        try {

            // Initialize notification
            $notification = [];

            $form = new Form();
            $form->fill($request->formAttributes());
            $form->price = $requestData['total_price'];

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
                    $recipientConnection = Rio::whereId($requestData['supplier']['id'])->first();

                    // Set RIO notification receiver
                    $notification += [
                        'rio_id' => $requestData['supplier']['id'],
                        'notification_content' => __('Notification Content - Form Recipient', [
                            'sender_name' => $senderName,
                            'form_type' => __('Purchase order'),
                        ]),
                    ];
                } else {
                    $form->supplier_neo_id = $requestData['supplier']['id'];

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
                            'form_type' => __('Purchase order'),
                        ]),
                    ];
                }
                $form->supplier_name = $requestData['supplier_name'];
            } else {
                unset($requestData['supplier']);
                $form->supplier_name = $requestData['supplier_name'];
            }

            $form->created_rio_id = $user->rio_id;
            $form->save();

            // Create records in histories table for every record created for purchase orders
            $formHistory = new FormHistory();
            $formHistory->fill($form->toArray());
            $formHistory->form_id = $form->id;
            $formHistory->operation_datetime = Carbon::now();
            $formHistory->operation_details = OperationDetailHelper::getRegistrationMessage($form->title);
            $formHistory->operator_email = $user->email;
            $formHistory->save();

            // Generate target directory
            $targetDirectory = config('bphero.form_issuer_image');
            $historyDirectory = config('bphero.form_histories_issuer_image') . $formHistory->form_id;

            // Handle saving of issuer image
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
                $fileExtension =  pathinfo($uploadFilename);
                $extension = $fileExtension['extension'] ?? '';
                $historyImagePath = "{$historyDirectory}/{$formHistory->id}_{$currentDate}.{$extension}";
                $formImagePath = "{$targetDirectory}/{$targetFilename}";


                // Handle invalid service type session
                if (empty($targetDirectory)) {
                    throw new ServiceSessionNotFoundException();
                }

                // Transfer temporary file to permanent directory
                $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

                // Create a copy to forms histories image directory
                Storage::disk(config('bphero.public_bucket'))->copy($formImagePath, $historyImagePath);

                $form->issuer_image = $targetFilename;
                $form->save();

                $formHistory->issuer_image = Storage::disk(config('bphero.public_bucket'))->url($historyImagePath);
                $formHistory->save();
            } else {
                $formImage = '';
                $formHistoryImage = '';

                switch ($requestData['mode']) {
                    case OperationTypes::DUPLICATE:
                        $formImage = Form::issuerImageDuplicate($form);
                        $formHistoryImage = Form::issuerImageDuplicate($form, $formHistory);
                        break;
                    default:
                        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();
                        $formBasicSettingsImage = $formBasicSetting->image ?? null;

                        if (empty($formBasicSetting) || empty($formBasicSettingsImage)) {
                            $formBasicSetting = FormBasicSetting::getSettingFromRioNeo($service);
                        }

                        if ($formBasicSetting && $formBasicSetting->image) {
                            $formImage = $formBasicSetting->image;
                            $formHistoryImage = $formBasicSetting->image;
                        }
                        break;
                }

                $form->issuer_image = $formImage;
                $form->save();

                $formHistory->issuer_image = $formHistoryImage;
                $formHistory->save();
            }

            // Create product records for purchase orders
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
                'message' => __('The purchase order has been issued'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);

            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Update purchase Order
     *
     * @param CreatePurchaseOrderRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function updatePurchaseOrder(CreatePurchaseOrderRequest $request, Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();
        $currentDate =  Carbon::now()->format('YmdHis');
        $operationMessage = OperationDetailHelper::getEditMessage($form, $requestData, Types::PURCHASE_ORDER);
        $formHistory = new FormHistory();

        DB::beginTransaction();

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

            $form->supplier_rio_id = null;
            $form->supplier_neo_id = null;
            $form->supplier_name = null;

            if (
                isset($requestData['is_supplier_connected']) &&
                !is_null($requestData['is_supplier_connected']) &&
                ($requestData['is_supplier_connected'] === true)
            ) {
                if ($requestData['supplier']['service'] === ServiceSelectionTypes::RIO) {
                    $form->supplier_rio_id = $requestData['supplier']['id'];
                    $recipientConnection = Rio::whereId($requestData['supplier']['id'])->first();

                    // Set RIO notification receiver
                    $notification += [
                        'rio_id' => $requestData['supplier']['id'],
                        'notification_content' => __('Notification Content - Form Recipient', [
                            'sender_name' => $senderName,
                            'form_type' => __('Purchase order'),
                        ]),
                    ];
                } else {
                    $form->supplier_neo_id = $requestData['supplier']['id'];

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
                            'form_type' => __('Purchase order'),
                        ]),
                    ];
                }
                $form->supplier_name = $requestData['supplier_name'];
            } else {
                unset($requestData['supplier']);
                $form->supplier_name = $requestData['supplier_name'];
            }

            // Update record in purchase order
            $form->fill($request->formAttributes());
            $form->price = $requestData['total_price'];
            $form->save();

            // Create records in histories table for every purchase order update
            if (!empty($operationMessage)) {
                // Fetch latest form history record for given form id to get image
                $history = FormHistory::whereFormId($form->id)
                    ->orderBy('operation_datetime', 'desc')
                    ->first();

                $formHistory->fill($form->toArray());
                $formHistory->form_id = $form->id;
                $formHistory->operation_datetime = Carbon::now();
                $formHistory->operation_details = $operationMessage;
                $formHistory->operator_email = $user->email;
                $formHistory->save();

                // Delete all existing products in purchase order for given form id
                $form->products()->delete();

                // Enter new set of product records for the given form id
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
            }

            // Handle saving of issuer image
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

                // Generate target directory
                $targetDirectory = config('bphero.form_issuer_image');
                $historyDirectory = config('bphero.form_histories_issuer_image') . $formHistory->form_id;

                $targetFilename = $form->id . '_' . $uploadFilename;
                $fileExtension =  pathinfo($uploadFilename);
                $extension = $fileExtension['extension'] ?? '';
                $historyImagePath = "{$historyDirectory}/{$formHistory->id}_{$currentDate}.{$extension}";
                $formImagePath = "{$targetDirectory}/{$targetFilename}";

                // Handle invalid service type session
                if (empty($targetDirectory)) {
                    throw new ServiceSessionNotFoundException();
                }

                $this->deleteS3File($formImagePath);

                // Transfer temporary file to permanent directory
                $fileinfo = $filepond->transferFile($targetDirectory, $targetFilename, false);

                // Create a copy to forms histories image directory
                Storage::disk(config('bphero.public_bucket'))->copy($formImagePath, $historyImagePath);

                $form->issuer_image = $targetFilename;
                $form->save();

                $formHistory->issuer_image = Storage::disk(config('bphero.public_bucket'))->url($historyImagePath);
                $formHistory->save();
            } else {
                if (!empty($operationMessage)) {
                    $formHistory->issuer_image = !empty($history) ? $history->issuer_image : FormHistory::generateIssuerImage($form->issuer_image, $form->id, $formHistory->id);
                    $formHistory->save();
                }

                // If image has been deleted, set image to null
                $form->issuer_image = null;
                $form->save();

                $formHistory->issuer_image = null;
                $formHistory->save();
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
                'message' => __('The purchase order has been updated'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $e) {
            report($e);
            DB::rollBack();

            Log::debug($e);

            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

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
     * Purchase Order CSV download page
     *
     * @return \Illuminate\View\View
     */
    public function csvDownloadList()
    {
        // Set form type
        $type = Types::PURCHASE_ORDER;

        return view('forms.csv-download', compact(
            'type',
        ));
    }
}
