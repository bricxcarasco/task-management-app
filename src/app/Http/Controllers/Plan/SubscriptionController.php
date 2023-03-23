<?php

namespace App\Http\Controllers\Plan;

use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaidPlan\VerifyIncompleteSubscriptionRequest;
use App\Http\Requests\Plan\PaymentMethodRegistrationRequest;
use App\Models\Plan;
use App\Models\Subscriber;
use App\Objects\ServiceSelected;
use App\Services\StripeService;
use Stripe\Exception\CardException;

class SubscriptionController extends Controller
{
    /**
     * Stripe Service
     *
     * @var StripeService
     */
    public $stripeService;

    /**
     * Controller constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->stripeService = new StripeService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();
        $serviceName = strtolower($service->type);

        // Get stripe customer id of service selected
        $stripeCustomerId = Subscriber::serviceSelected()
            ->first()
            ->stripe_customer_id ?? '';

        $subscriber = Subscriber::active();
        $freePlan = Plan::serviceSelected()->free()->first();

        if ($service->type === ServiceSelectionTypes::NEO) {
            $subscriber = $subscriber->where('neo_id', $service->data->id)->first();
        } else {
            $subscriber = $subscriber->where('rio_id', $user->rio_id)->first();
        }

        $subscription = $subscriber->plan ?? $freePlan;

        $cardPaymentMethod = $this->stripeService->getCardPaymentMethod($stripeCustomerId);
        $customer = $this->stripeService->getStripeCustomer($stripeCustomerId);

        return view('plans.subscription', compact(
            'service',
            'serviceName',
            'customer',
            'cardPaymentMethod',
            'subscription'
        ));
    }

    /**
     * Verify incomplete subscription
     *
     * @param VerifyIncompleteSubscriptionRequest $request
     *
     * @return mixed
     */
    public function verifyIncompleteSubscription(VerifyIncompleteSubscriptionRequest $request)
    {
        // Get validated data
        $requestData = $request->validated();

        try {
            /** @var \App\Models\User */
            $user = auth()->user();

            // Get subject selected session
            $service = ServiceSelected::getSelected();

            $subscriber = Subscriber::incompletePayment();
            $freePlan = Plan::serviceSelected()->free()->first();

            if ($service->type === ServiceSelectionTypes::NEO) {
                $subscriber = $subscriber->where('neo_id', $service->data->id)->first();
            } else {
                $subscriber = $subscriber->where('rio_id', $user->rio_id)->first();
            }

            $plan = $subscriber->plan ?? $freePlan;

            $subscription = Subscriber::incompletePayment()
                ->whereStripeCustomerId($requestData['stripe_customer_id'])
                ->whereStripeSubscriptionId($requestData['stripe_subscription_id'])
                ->whereStripeClientSecret($requestData['stripe_client_secret']);

            if ($service->type === ServiceSelectionTypes::NEO) {
                $subscription = $subscription->where('neo_id', $service->data->id)->first();
            } else {
                $subscription = $subscription->where('rio_id', $user->rio_id)->first();
            }

            return view('paid-plan.change-plan.payment-confirmation', compact(
                'plan',
                'subscription'
            ));
        } catch (\Exception $exception) {
            report($exception);

            session()->put('alert', [
                'status' => 'error',
                'message' => __('Server Error'),
            ]);

            return back();
        }
    }

    /**
     * Attaches created payment method to customer
     *
     * @param PaymentMethodRegistrationRequest $request
     * @return mixed
     */
    public function savePaymentMethod(PaymentMethodRegistrationRequest $request)
    {
        $paymentMethod = json_decode($request->payment_method);
        $customerId = $request->customer_id;
        $currentPaymentMethod = $this->stripeService->getCardPaymentMethod($customerId);

        try {
            if ($paymentMethod) {
                $this->stripeService->createCustomerSetupIntent($customerId, $paymentMethod->id);
                $this->stripeService->attachCardPaymentMethod($customerId, $paymentMethod);
                $this->stripeService->setCustomerDefaultPaymentMethod($customerId, $paymentMethod->id);
                $this->stripeService->detachPaymentMethod($currentPaymentMethod->data);

                return response()->respondSuccess([
                    'card' => $paymentMethod->card->last4,
                    'message' => __('Your payment information has been changed'),
                    'redirect_url' => route('plans.subscription')
                ]);
            }
        } catch (CardException $exception) {
            return response()->respondInvalidParameters([
                'decline_code' => $exception->getDeclineCode(),
            ], __('Card Declined'));
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Delete customer payment method
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePaymentMethod()
    {
        try {
            // Get stripe customer id of service selected
            $stripeCustomerId = Subscriber::serviceSelected()
                ->first()
                ->stripe_customer_id ?? '';

            $paymentMethod = $this->stripeService->getCardPaymentMethod($stripeCustomerId);
            $this->stripeService->detachPaymentMethod($paymentMethod->data);

            return response()->respondSuccess([
                'message' => __('Your payment information has been changed'),
            ]);
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }
}
