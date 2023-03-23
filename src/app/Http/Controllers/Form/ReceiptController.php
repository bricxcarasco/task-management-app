<?php

namespace App\Http\Controllers\Form;

use App\Enums\Form\Types;
use App\Enums\ServiceSelectionTypes;
use App\Helpers\OperationDetailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Form\CreateReceiptRequest;
use App\Http\Requests\Form\ReceiptSearchInputRequest;
use App\Http\Resources\Form\FormResource;
use App\Models\Form;
use App\Models\FormBasicSetting;
use App\Models\FormHistory;
use App\Models\Neo;
use App\Models\Notification;
use App\Models\Rio;
use App\Objects\FormProductCalculations;
use App\Objects\ServiceSelected;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    /**
     * Receipt creation page
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

        return view('forms.receipts.create', compact(
            'rio',
            'service',
            'formBasicSetting'
        ));
    }

    /**
     * Receipt list page
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

        return view('forms.receipts.index', compact(
            'rio',
            'service',
        ));
    }

    /**
     * Receipt edit page
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|void
     */
    public function edit(Request $request, Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $basicSettings = FormBasicSetting::serviceSetting($service)->first();

        $formBasicSetting = FormBasicSetting::serviceSetting($service)->first();

        return view('forms.receipts.edit', compact(
            'rio',
            'service',
            'form',
            'formBasicSetting'
        ));
    }

    /**
     * Display receipt details.
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
            ->formDetails(Types::RECEIPT)
            ->where('forms.id', $form->id)
            ->firstOrFail();

        // Calculate product prices and taxes
        $pricesAndTaxes = FormProductCalculations::getPricesAndTaxes($form->products);

        return view('forms.receipts.show', compact(
            'form',
            'service',
            'pricesAndTaxes',
        ));
    }

    /**
     * Get receipt lists
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function getReceiptLists(Request $request)
    {
        $requestData = $request->all();

        $receipt = Form::formList(Types::RECEIPT)
            ->commonConditions($requestData)
            ->paginate(config('bphero.paginate_count'));

        return FormResource::collection($receipt);
    }

    /**
     * Validate receipt search
     *
     * @param \App\Http\Requests\Form\ReceiptSearchInputRequest $request
     * @return mixed
     */
    public function validateReceiptSearch(ReceiptSearchInputRequest $request)
    {
        $requestData = $request->validated();

        return response()->respondSuccess($requestData);
    }

    /**
     * Confirm receipt
     *
     * @param \App\Http\Requests\Form\CreateReceiptRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function confirm(CreateReceiptRequest $request)
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

        try {
            // Initialize notification
            $notification = [];

            $form = new Form();
            $form->fill($request->formAttributes());
            $form->refer_receipt_no = $requestData['refer_receipt_no'];
            $form->price = $requestData['receipt_amount'];

            $formHistory = new FormHistory();
            $formHistory->fill($request->formAttributes());
            $formHistory->refer_receipt_no = $requestData['refer_receipt_no'];
            $formHistory->price = $requestData['receipt_amount'];

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
                            'form_type' => __('Receipt'),
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
                            'form_type' => __('Receipt'),
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

            $formHistory->operation_datetime = Carbon::now();
            $formHistory->operation_details = OperationDetailHelper::getRegistrationMessage($form->title);
            $formHistory->operator_email = $user->email;
            $formHistory->form_id = $form->id;
            $formHistory->created_rio_id = $user->rio_id;
            $formHistory->save();

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
                'message' => __('The receipt has been issued'),
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
     * Update receipt
     *
     * @param \App\Http\Requests\Form\CreateReceiptRequest $request
     * @param \App\Models\Form $form
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Symfony\Component\HttpFoundation\Response
     */
    public function updateReceipt(CreateReceiptRequest $request, Form $form)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get request data
        $requestData = $request->validated();

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

            // Get helper for edit function
            $operationDetails = OperationDetailHelper::getEditMessage($form, $requestData, Types::RECEIPT);

            if (!empty($operationDetails)) {
                $form->supplier_rio_id = null;
                $form->supplier_neo_id = null;
                $form->supplier_name = null;
            }

            $form->fill($request->formAttributes());
            $form->refer_receipt_no = $requestData['refer_receipt_no'];
            $form->price = $requestData['receipt_amount'];

            $formHistory = new FormHistory();
            $formHistory->fill($request->formAttributes());
            $formHistory->operation_datetime = Carbon::now();
            $formHistory->operator_email = $user->email;
            $formHistory->form_id = $form->id;
            $formHistory->created_rio_id = $user->rio_id;
            $formHistory->refer_receipt_no = $requestData['refer_receipt_no'];
            $formHistory->price = $requestData['receipt_amount'];

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
                            'form_type' => __('Receipt'),
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
                            'form_type' => __('Receipt'),
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
                'message' => __('The receipt has been updated'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::debug($exception);
            report($exception);

            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Form receipt deletion.
     *
     * @param \App\Models\Form $form
     * @param bool $withAlert. Defaults to false.
     * @return mixed
     */
    public function destroy(Form $form, $withAlert = false)
    {
        $this->authorize('delete', [Form::class, $form, Types::RECEIPT]);

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
                route('forms.receipts.index')
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
     * Form receipt create duplicate.
     *
     * @param \App\Models\Form $form
     * @return \Illuminate\Contracts\View\View|void
     */
    public function duplicate(Form $form)
    {
        $this->authorize('createFromDiffForm', [Form::class, $form, Types::RECEIPT]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        $formBasicSetting = FormBasicSetting::currentEntityServiceSetting()->first();

        // Unset properties that will not be used
        unset(
            $form->issue_date,
            $form->receipt_date,
        );

        // Adjust mismatched properties
        $form->receipt_amount = (int)$form->price;

        return view('forms.receipts.duplicate', compact(
            'rio',
            'service',
            'formBasicSetting',
            'form'
        ));
    }

    /**
     * Receipt CSV download page
     *
     * @return \Illuminate\View\View
     */
    public function csvDownloadList()
    {
        // Set form type
        $type = Types::RECEIPT;

        return view('forms.csv-download', compact(
            'type',
        ));
    }
}
