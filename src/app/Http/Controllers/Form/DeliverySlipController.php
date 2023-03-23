<?php

namespace App\Http\Controllers\Form;

use App\Enums\Form\OperationTypes;
use App\Enums\Form\Types;
use App\Enums\ServiceSelectionTypes;
use App\Exceptions\ServiceSessionNotFoundException;
use App\Helpers\OperationDetailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Form\CreateDeliverySlipRequest;
use App\Http\Requests\Form\DeliverySlipSearchInputRequest;
use App\Http\Resources\Form\FormResource;
use App\Models\Form;
use App\Models\FormBasicSetting;
use App\Models\FormHistory;
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

class DeliverySlipController extends Controller
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
     * Delivery Slip create page
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
        $basicSettings = FormBasicSetting::serviceSetting($service)->first();

        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

        return view('forms.delivery-slips.create', compact(
            'rio',
            'service',
            'formBasicSetting'
        ));
    }

    /**
     * Display Delivery Slips details.
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
            ->formDetails(Types::DELIVERY_SLIP)
            ->where('forms.id', $form->id)
            ->firstOrFail();

        // Calculate product prices and taxes
        $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

        return view('forms.delivery-slips.show', compact(
            'form',
            'service',
            'pricesAndTaxes'
        ));
    }

    /**
     * Delivery Slip edit page.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|void
     */
    public function edit(Request $request, Form $form)
    {
        $this->authorize('edit', [Form::class, $form, Types::DELIVERY_SLIP]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

        if (empty($formBasicSetting)) {
            $formBasicSetting = FormBasicSetting::getSettingFromRioNeo($service);
        }

        return view('forms.delivery-slips.edit', compact(
            'rio',
            'service',
            'form',
            'formBasicSetting'
        ));
    }

    /**
     * Delivery Slip list page
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

        return view('forms.delivery-slips.index', compact(
            'rio',
            'service',
        ));
    }

    /**
     * Create delivery slip
     *
     * @param CreateDeliverySlipRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function createDeliverySlip(CreateDeliverySlipRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        if ($requestData['mode'] == 'validation') {
            return response()->json($requestData);
        }

        return response()->json(['save' => 'save']);
    }

    /**
     * Confirm delivery slip
     *
     * @param \App\Http\Requests\Form\CreateDeliverySlipRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function confirm(CreateDeliverySlipRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $senderName = null;
        $recipientConnection = null;

        // Initialize notification
        $notification = [];

        try {
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
                            'form_type' => __('Delivery slip'),
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
                            'form_type' => __('Delivery slip'),
                        ]),
                    ];
                }
                $form->supplier_name = $requestData['supplier_name'];
                $formHistory->supplier_name = $requestData['supplier_name'];
            } else {
                unset($requestData['supplier']);
                $form->supplier_name = $requestData['supplier_name'];
                $formHistory->supplier_name = $requestData['supplier_name'];
                $isManuallyEnteredSupplier = true;
            }

            $form->created_rio_id = $user->rio_id;
            $form->save();

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
                'message' => __('The delivery slip has been issued'),
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
     * Confirm delivery slip
     *
     * @param \App\Http\Requests\Form\CreateDeliverySlipRequest $request
     * @param \App\Models\Form $form
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function updateDeliverySlip(CreateDeliverySlipRequest $request, Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        // Get helper for edit function
        $operationDetails = OperationDetailHelper::getEditMessage($form, $requestData, Types::DELIVERY_SLIP);

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

        // Initialize notification
        $notification = [];

        if ($service->type === ServiceSelectionTypes::NEO) {
            $senderName = $service->data->organization_name;
        } else {
            $senderName = $service->data->full_name . 'さん';
        }

        try {
            $formHistory = new FormHistory();
            $formHistory->fill($request->formAttributes());
            $formHistory->operation_datetime = Carbon::now();
            $formHistory->operator_email = $user->email;
            $formHistory->form_id = $form->id;
            $formHistory->created_rio_id = $user->rio_id;
            $formHistory->issuer_image = $formHistory->getMissingPath($storagePath, $formHistory) ?? null;

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
                            'form_type' => __('Delivery slip'),
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
                            'form_type' => __('Delivery slip'),
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

                // Delete existing product
                $form->products()->delete();
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
                'message' => __('The delivery slip has been updated'),
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
     * Get Delivery Slip lists
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getDeliverySlipLists(Request $request)
    {
        $requestData = $request->all();

        $deliverySlip = Form::formList(Types::DELIVERY_SLIP)
            ->commonConditions($requestData)
            ->paginate(config('bphero.paginate_count'));

        return FormResource::collection($deliverySlip);
    }

    /**
     * Validate Delivery Slip search
     *
     * @param \App\Http\Requests\Form\DeliverySlipSearchInputRequest $request
     * @return mixed
     */
    public function validateDeliverySlipSearch(DeliverySlipSearchInputRequest $request)
    {
        $requestData = $request->validated();

        return response()->respondSuccess($requestData);
    }

    /**
     * Form delivery slip deletion.
     *
     * @param \App\Models\Form $form
     * @param bool $withAlert. Defaults to false.
     * @return mixed
     */
    public function destroy(Form $form, $withAlert = false)
    {
        $this->authorize('delete', [Form::class, $form, Types::DELIVERY_SLIP]);

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
                route('forms.delivery-slips.index')
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
     * Form delivery slip create duplicate.
     *
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|void
     */
    public function duplicate(Form $form)
    {
        $this->authorize('createFromDiffForm', [Form::class, $form, Types::DELIVERY_SLIP]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $formBasicSetting = FormBasicSetting::currentEntityServiceSetting()->first();

        // Unset properties that will not be used
        unset(
            $form->form_no,
            $form->delivery_date,
            $form->delivery_deadline,
            $form->issue_date,
            $form->payment_date,
            $form->expiration_date,
            $form->receipt_date,
        );

        return view('forms.delivery-slips.duplicate', compact(
            'rio',
            'service',
            'formBasicSetting',
            'form'
        ));
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
     * Delivery slip CSV download page
     *
     * @return \Illuminate\View\View
     */
    public function csvDownloadList()
    {
        // Set form type
        $type = Types::DELIVERY_SLIP;

        return view('forms.csv-download', compact(
            'type',
        ));
    }
}
