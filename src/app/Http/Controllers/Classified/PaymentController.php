<?php

namespace App\Http\Controllers\Classified;

use App\Events\Classified\ReceiveMessages;
use App\Enums\Classified\MessageSender;
use App\Enums\Classified\PaymentMethods;
use App\Enums\Classified\PaymentStatuses;
use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classified\PaymentIssuanceRequest;
use App\Http\Requests\Classified\ProcessPaymentRequest;
use App\Models\ClassifiedContact;
use App\Models\ClassifiedContactMessage;
use App\Models\ClassifiedPayment;
use App\Models\ClassifiedSale;
use App\Models\ClassifiedSetting;
use App\Models\Neo;
use App\Models\Rio;
use App\Services\StripeService;
use DB;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentController extends Controller
{
    /**
     * Issue new payment and generate URL.
     *
     * @param \App\Http\Requests\Classified\PaymentIssuanceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function issuePaymentUrl(PaymentIssuanceRequest $request)
    {
        // Get request data
        $requestData = $request->validated();

        DB::beginTransaction();

        $contact = ClassifiedContact::whereId($requestData['classified_contact_id'])->firstOrFail();

        try {
            // Generate access_key and payment URL
            $length = config('bphero.payment_access_key_length');
            $accessKey = CommonHelper::randomAlphaNumericString($length);
            $paymentUrl = route('classifieds.payments.access-payment', $accessKey);

            // Create new payment
            $payment = new ClassifiedPayment();
            $payment->fill($requestData);
            $payment->access_key = $accessKey;
            $payment->status = PaymentStatuses::PENDING;
            $payment->save();

            // Send payment URL as new message
            $message = new ClassifiedContactMessage();
            $message->classified_contact_id = $requestData['classified_contact_id'];
            $message->sender = MessageSender::SELLER;
            $message->message = $paymentUrl;
            $message->attaches = null;
            $message->save();

            DB::commit();

            //Send buyer mail notification of purchase
            $seller = Rio::whereId($contact->selling_rio_id)->first()
                ?? Neo::whereId($contact->selling_neo_id)->first();

            $buyer = Rio::whereId($contact->rio_id)->first()
                ?? Neo::whereId($contact->neo_id)->first();

            $product = ClassifiedSale::whereId($contact->classified_sale_id)->first();
            $payment->sendNetshopPurchaseEmail($seller, $buyer, $product);

            // Broadcast message to channels
            ReceiveMessages::dispatch($requestData['classified_contact_id']);

            return response()->respondSuccess($paymentUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return response()->respondInternalServerError([$e->getMessage()]);
        }
    }

    /**
     * Payment page.
     *
     * @param string $accessKey
     * @return \Illuminate\View\View
     */
    public function payment(string $accessKey)
    {
        // Get payment information
        /** @var ClassifiedPayment */
        $payment = ClassifiedPayment::accessiblePayments()
            ->whereAccessKey($accessKey)
            ->whereStatus(PaymentStatuses::PENDING)
            ->firstOrFail();

        // Guard clause for non-accessible payment URL
        $this->authorize('accessUrl', [ClassifiedPayment::class, $payment]);

        // Get displayable photo
        $payment['main_photo'] = $payment->classified_sale->main_photo;

        /** @var \App\Models\ClassifiedSale */
        $sale = $payment->classified_sale;

        // Get seller setting information
        $sellerSetting = ClassifiedSetting::whereRioId($sale->selling_rio_id)
            ->whereNeoId($sale->selling_neo_id)
            ->firstOrFail();

        if ($payment->payment_method === PaymentMethods::CARD) {
            $intent = null;

            // Get stripe setting
            $cardSetting = ClassifiedSetting::getCardSettings($sellerSetting);
            $stripeInfo = [
                'publishable_key' => config('stripe.publishable_key'),
                'secret_key' => config('stripe.secret_key'),
            ];

            try {
                if ($payment->isBuyer()) {
                    // Render 404 if no Stripe account
                    if (!isset($cardSetting['account_id'])) {
                        abort(404);
                    }

                    $amount = number_format(ceil((float)$payment->price), 0, '', '');
                    $appFeeAmount = StripeService::calculateApplicationFeeAmount($amount);

                    // Create Stripe configuration
                    Stripe::setApiKey($stripeInfo['secret_key']);
                    $intent = PaymentIntent::create(
                        [
                            'amount' => $amount,
                            'currency' => config('stripe.currency'),
                            'application_fee_amount' => $appFeeAmount,
                        ],
                        ['stripe_account' => $cardSetting['account_id']]
                    );
                }

                return view('classifieds.payments.card-payment', compact(
                    'payment',
                    'stripeInfo',
                    'cardSetting',
                    'intent',
                ));
            } catch (\Exception $exception) {
                // Report and render 500
                report($exception);
                abort(500);
            }
        } elseif ($payment->payment_method === PaymentMethods::TRANSFER) {
            // Get bank accounts
            $bankAccounts = ClassifiedSetting::getBankAccounts($sellerSetting);

            // Render 404 if no bank accounts registered
            if (empty($bankAccounts)) {
                abort(404);
            }

            return view('classifieds.payments.bank-transfer', compact(
                'payment',
                'bankAccounts',
            ));
        }

        // Render 404 if neither card or bank transfer
        abort(404);
    }

    /**
     * Process credit card payment using Stripe.
     *
     * @param \App\Http\Requests\Classified\ProcessPaymentRequest $request
     * @return mixed
     */
    public function processPayment(ProcessPaymentRequest $request)
    {
        // Get validated data
        $requestData = $request->validated();

        try {
            // Get payment intent data
            $paymentIntent = json_decode($requestData['intent']);

            if ($paymentIntent->status === 'succeeded') {
                $payment = ClassifiedPayment::whereId($requestData['payment_id'])
                    ->firstOrFail();

                // Update payment
                $payment->update([
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'status' => PaymentStatuses::AUTOMATICALLY_COMPLETED,
                ]);

                session()->put('alert', [
                    'status' => 'success',
                    'message' => __('Successfully paid the product'),
                ]);
            } else {
                session()->put('alert', [
                    'status' => 'danger',
                    'message' => __('Payment failed'),
                ]);
            }

            return response()->respondSuccess(
                route('classifieds.sales.index')
            );
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }
}
