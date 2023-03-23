<?php

namespace App\Http\Controllers\PaidPlan;

use App\Enums\PlanServiceType;
use App\Enums\ServiceSelectionTypes;
use App\Enums\PaidPlan\ServiceType;
use App\Enums\PaidPlan\StatusType;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaidPlan\ProcessPaymentRequest;
use App\Helpers\CommonHelper;
use App\Models\Document;
use App\Models\Plan;
use App\Models\PlanService;
use App\Models\ServiceSetting;
use App\Models\Subscriber;
use App\Models\SubscriptionHistory;
use App\Objects\ServiceSelected;
use App\Services\StripeService;
use Carbon\Carbon;
use DB;

class PaidPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     *@return \Illuminate\View\View
     */
    public function index()
    {
        $service = ServiceSelected::getSelected();

        return view('paid-plan.index', compact('service'));
    }

    /**
     * Process credit card payment using Stripe.
     *
     * @param \App\Http\Requests\PaidPlan\ProcessPaymentRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment(ProcessPaymentRequest $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get validated data
        $requestData = $request->validated();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        try {
            DB::beginTransaction();

            // Get payment intent data
            $paymentIntent = json_decode($requestData['intent']);

            if ($paymentIntent->status === 'succeeded') {
                $subscriber = Subscriber::whereId($requestData['subscriber_id'])
                    ->firstOrFail();

                // Get subscription data from Stripe
                $subscriptionService = new StripeService();
                $subscription = $subscriptionService->getSubscription($subscriber->stripe_subscription_id);

                $subscriptionHistory = [
                    'total_price' => $subscriber->total_price,
                    'data' => $subscriber->stripe_client_secret
                ];

                // Update subscriber
                $subscriber->update([
                    'status' => StatusType::ACTIVE,
                    'start_date' => Carbon::createFromTimestamp($subscription->current_period_start),
                    'end_date' => Carbon::createFromTimestamp($subscription->current_period_end),
                    'stripe_client_secret' => null,
                ]);

                if (!is_null($subscriber->rio_id)) {
                    $subscriptionHistory['rio_id'] = $subscriber->rio_id;
                } else {
                    $subscriptionHistory['neo_id'] = $subscriber->neo_id;
                }

                // Prepare initial/default data
                $usedStorage = (int) Document::totalStorageUsed($service->data);
                $default = [
                    'max_storage' => 0,
                    'used' => $usedStorage,
                    'available' => 0
                ];

                /** @var \App\Models\PlanService */
                $planService = PlanService::wherePlanId($subscriber->plan_id)
                    ->whereServiceId(ServiceType::DOCUMENT_MANAGEMENT)
                    ->whereType(PlanServiceType::PLAN)
                    ->first();

                /** @var \App\Models\ServiceSetting */
                $settings = new ServiceSetting();

                // Get service settings
                if ($service->type === ServiceSelectionTypes::RIO) {
                    /** @var \App\Models\ServiceSetting */
                    $settings = ServiceSetting::whereRioId($user->rio_id)->first();
                } else {
                    /** @var \App\Models\ServiceSetting */
                    $settings = ServiceSetting::whereNeoId($service->data->id)->first();
                }

                // Prepare storage info - Plan Based Only
                $storage = CommonHelper::convertToBytes($planService->unit, (int) $planService->value);

                $available = $storage - $usedStorage;
                $storageInfo = [
                    'max_storage' => $storage,
                    'used' => $usedStorage,
                    'available' => $available < 0 ? 0 : $available,
                ];

                // Update storage info in record
                /** @phpstan-ignore-next-line */
                $settings->data = json_encode($storageInfo);
                $settings->save();

                SubscriptionHistory::create($subscriptionHistory);

                DB::commit();

                return response()->respondSuccess([
                    'redirect_url' => route('plans.subscription'),
                    'status' => 'success',
                    'message' => __('Successfully paid the product')
                ]);
            } else {
                return response()->respondSuccess([
                    'redirect_url' => '',
                    'status' => 'danger',
                    'message' => __('Payment failed')
                ]);
            }
        } catch (\Exception $exception) {
            report($exception);

            DB::rollback();

            return response()->respondInternalServerError();
        }
    }

    /**
     * Add Document Management.
     *
     *@return \Illuminate\View\View
     */
    public function addDocumentManagement()
    {
        return view('paid-plan.add-option.add-document-management');
    }

    /**
     * Document Management Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function documentManagementConfirmation()
    {
        return view('paid-plan.add-option.document-management-confirmation');
    }

    /**
     * Add staff.
     *
     *@return \Illuminate\View\View
     */
    public function addStaff()
    {
        return view('paid-plan.add-option.add-staff');
    }

    /**
     * Add staff Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function addStaffConfirmation()
    {
        return view('paid-plan.add-option.add-staff-confirmation');
    }

    /**
     * Add Net Shop.
     *
     *@return \Illuminate\View\View
     */
    public function addNetShop()
    {
        return view('paid-plan.add-option.add-net-shop');
    }

    /**
     * Net Shop Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function netShopConfirmation()
    {
        return view('paid-plan.add-option.net-shop-confirmation');
    }

    /**
     * Add Invoice.
     *
     *@return \Illuminate\View\View
     */
    public function addInvoice()
    {
        return view('paid-plan.add-option.add-invoice');
    }

    /**
     * Invoice Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function invoiceConfirmation()
    {
        return view('paid-plan.add-option.invoice-confirmation');
    }

    /**
     * Add Purchase Order.
     *
     *@return \Illuminate\View\View
     */
    public function addPurchaseOrder()
    {
        return view('paid-plan.add-option.add-purchase-order');
    }

    /**
     * Purchase Order Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function purchaseOrderConfirmation()
    {
        return view('paid-plan.add-option.purchase-order-confirmation');
    }

    /**
     * Add Delivery Note.
     *
     *@return \Illuminate\View\View
     */
    public function addDeliveryNote()
    {
        return view('paid-plan.add-option.add-delivery-note');
    }

    /**
     * Delivery Note Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function deliveryNoteConfirmation()
    {
        return view('paid-plan.add-option.delivery-note-confirmation');
    }

    /**
     * Add Receipt.
     *
     *@return \Illuminate\View\View
     */
    public function addReceipt()
    {
        return view('paid-plan.add-option.add-receipt');
    }

    /**
     * Receipt Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function receiptConfirmation()
    {
        return view('paid-plan.add-option.receipt-confirmation');
    }

    /**
     * Add Workflow.
     *
     *@return \Illuminate\View\View
     */
    public function addWorkflow()
    {
        return view('paid-plan.add-option.add-workflow');
    }

    /**
     * Workflow Payment Confirmation.
     *
     *@return \Illuminate\View\View
     */
    public function workflowConfirmation()
    {
        return view('paid-plan.add-option.workflow-confirmation');
    }
}
