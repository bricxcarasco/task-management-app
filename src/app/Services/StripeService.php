<?php

namespace App\Services;

use Stripe\StripeClient;
use App\Objects\ServiceSelected;
use App\Enums\Classified\BusinessType;
use App\Enums\Classified\StripeRequirementErrors;
use App\Enums\PaidPlan\PaymentMethodType;
use App\Enums\PaidPlan\StatusType;
use App\Enums\Rio\GenderType;
use App\Enums\ServiceSelectionTypes;
use App\Models\ClassifiedSetting;
use App\Models\Neo;
use App\Models\User;
use App\Models\Subscriber;
use Carbon\Carbon;
use Stripe\Exception\PermissionException;

class StripeService
{
    /**
     * Stripe Client
     *
     * @var StripeClient
     */
    public $stripe;

    /**
     * Controller constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->stripe = new StripeClient(config('stripe.secret_key'));
    }

    /**
     * Get all main plans
     *
     * @return mixed
     */
    public function getPlans()
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $service = ServiceSelected::getSelected();

        try {
            $planId = config('stripe.rio_main_plan_product_id');

            if ($service->type == ServiceSelectionTypes::NEO) {
                $planId = config('stripe.neo_main_plan_product_id');
                ;
            }

            $plans = $this->stripe->prices->all(
                [
                    'product' => $planId
                ]
            );

            return $plans;
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Retrieve a single main plan
     *
     * @param string $priceId
     *
     * @return mixed
     */
    public function getPlan($priceId)
    {
        $plan = $this->stripe->prices->retrieve(
            $priceId,
            []
        );

        return $plan;
    }

    /**
     * Create or get Stripe account information from database.
     *
     * @param \App\Models\Plan $plan
     *
     * @return array
     */
    public function getCustomerInfo($plan)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $service = ServiceSelected::getSelected();

        try {
            $customer = null;

            // NOTE: Retrieving part of customer info will be change (database and table schema still on discuss)
            if ($service->type == ServiceSelectionTypes::NEO) {
                $customer = Subscriber::where('neo_id', $service->data->id)->first();
            } else {
                $customer = Subscriber::where('rio_id', $user->rio_id)->first();
            }

            if (!$customer) {
                $customerAccount = [];
                $subscriber = new Subscriber();

                if ($service->type == ServiceSelectionTypes::NEO) {
                    $customerAccount = [
                        'name' => $service->data->organization_name,
                        'email' => $service->data->email,
                        'phone' => $service->data->tel,
                        'description' => ServiceSelectionTypes::NEO
                    ];

                    $subscriber->neo_id = $service->data->id;
                } else {
                    $customerAccount = [
                        'name' => $service->data->full_name,
                        'email' => $user->email,
                        'phone' => $service->data->tel,
                        'description' => ServiceSelectionTypes::RIO
                    ];

                    $subscriber->rio_id = $user->rio_id;
                }

                // Create stripe customer info
                $stripeCustomer = $this->stripe->customers->create($customerAccount);

                $subscription = $this->createSubscription($stripeCustomer->id, $plan->stripe_price_id);

                $subscriber->stripe_customer_id = $stripeCustomer->id;
                $subscriber->plan_id = $plan->id;
                $subscriber->stripe_subscription_id = $subscription['subscription_id'];
                $subscriber->stripe_client_secret = $subscription['client_secret'];
                $subscriber->status = StatusType::INCOMPLETE;
                $subscriber->start_date = Carbon::now();
                $subscriber->end_date = Carbon::now();
                $subscriber->payment_method = PaymentMethodType::STRIPE;
                $subscriber->total_price = $plan->price;
                $subscriber->save();

                return [
                    'subscriber' => $subscriber,
                    'exists' =>  false
                ];
            }

            return [
                'subscriber' => $customer,
                'exists' =>  true
            ];
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Create Stripe subscription and payment intent instance
     *
     * @param string $customerId
     * @param string $priceId
     *
     * @return array
     */
    public function createSubscription($customerId, $priceId)
    {
        try {
            // Create Stripe subscription instance
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $customerId,
                'items' => [[
                    'price' => $priceId,
                ]],
                'payment_behavior' => 'default_incomplete',
                'payment_settings' => [
                    'save_default_payment_method' => 'on_subscription'
                ],
                'expand' => [
                    'latest_invoice.payment_intent'
                ],
            ]);

            return [
                'subscription_id' => $subscription->id,
                /** @phpstan-ignore-next-line */
                'client_secret' => $subscription->latest_invoice->payment_intent->client_secret
            ];
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Get Stripe subscription and payment intent instance
     *
     * @param string $subscriptionId
     *
     * @return mixed
     */
    public function getSubscription($subscriptionId)
    {
        try {
            // Get Stripe subscription instance
            $subscription = $this->stripe->subscriptions->retrieve(
                $subscriptionId,
                []
            );

            return $subscription;
        } catch (\Exception $exception) {
            report($exception);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Cancel Stripe subscription and payment intent instance
     *
     * @param \App\Models\Subscription $subscription
     *
     * @return mixed
     */
    public function cancelPaymentIntent($subscription)
    {
        try {
            // Get Stripe subscription instance
            $pendingSubscription = $this->getSubscription($subscription->stripe_subscription_id);

            $invoice = $this->stripe->invoices->voidInvoice(
                $pendingSubscription->latest_invoice,
                []
            );

            // Cancel subscription if exists
            if ($invoice->status == 'void') {
                $subscription->update([
                    'status' => StatusType::CANCELLED,
                ]);
                $subscription->delete();
            }

            return response()->respondSuccess();
        } catch (\Exception $exception) {
            report($exception);


            return response()->respondInternalServerError();
        }
    }

    /**
     * Get Stripe account information fron database.
     *
     * @param \App\Models\ClassifiedSetting|null $setting
     * @return array
     */
    public static function getStripeInfo($setting = null)
    {
        if (empty($setting)) {
            // Get card payment configuration
            $setting = ClassifiedSetting::cardPaymentSetting()->firstOrFail();
        }

        // Guard clause if empty stripe settings
        if (empty($setting->settings_by_card)) {
            return [];
        }

        // Decode card settings
        $config = json_decode($setting->settings_by_card, true);

        // Return empty array for invalid Stripe information
        if (
            !isset($config['account_id']) ||
            !isset($config['is_completed']) ||
            !isset($config['is_pending'])
        ) {
            return [];
        }

        return $config;
    }

    /**
     * Generate account information for Stripe account creation.
     *
     * @param object $service
     * @return array
     */
    public static function generateAccountInfo($service)
    {
        if ($service->type == ServiceSelectionTypes::NEO) {
            return self::generateNeoAccountInfo($service);
        } else {
            return self::generateRioAccountInfo($service);
        }
    }

    /**
     * Initialize Stripe Account information.
     *
     * @return array
     */
    private static function initializeAccountInformation()
    {
        return [
            'type' => 'standard',
            'country' => 'JP',
        ];
    }

    /**
     * Generate NEO company account information.
     *
     * @param object $service
     * @return array
     */
    private static function generateNeoAccountInfo($service)
    {
        /** @var \App\Models\Neo */
        $neo = Neo::whereId($service->data->id)->firstOrFail();

        // Guard clause for non-existing NEO owner
        if (empty($neo->owner) || empty($neo->owner->rio)) {
            abort(400);
        }

        // Guard clause for non-existing NEO profile
        if (empty($neo->neo_profile)) {
            abort(400);
        }

        /** @var \App\Models\Rio */
        $rioOwner = $neo->owner->rio;

        /** @var \App\Models\NeoProfile */
        $neoProfile = $neo->neo_profile;

        /** @var \App\Models\User */
        $user = User::whereRioId($rioOwner->id)->firstOrFail();

        // Initialize account information
        $accountInformation = self::initializeAccountInformation();

        // Set NEO account information
        $accountInformation += [
            'email' => $user->email,
            'business_type' => BusinessType::COMPANY,
            'business_profile' => [
                'name' => $neo->organization_name,
                'url' => $neo->site_url,
                'product_description' => $neoProfile->self_introduce ?? null,
            ],
            'company' => [
                'name' => $neo->organization_name,
            ],
        ];

        return $accountInformation;
    }

    /**
     * Generate RIO individual account information.
     *
     * @param object $service
     * @return array
     */
    private static function generateRioAccountInfo($service)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        /** @var \App\Models\RioProfile */
        $rioProfile = $rio->rio_profile;

        // Initialize account information
        $accountInformation = self::initializeAccountInformation();

        // Set RIO account information
        $accountInformation += [
            'email' => $user->email,
            'business_type' => BusinessType::INDIVIDUAL,
            'business_profile' => [
                'name' => $rio->full_name,
                'product_description' => $rioProfile->self_introduce ?? null,
            ],
            'individual' => [
                'first_name' => $rio->first_name,
                'last_name' => $rio->family_name,
                'gender' => strtolower(GenderType::getKey($rio->gender)),
                'phone' => $rio->tel,
                'email' => $user->email,
                'dob' => [
                    'day' => Carbon::parse($rio->birth_date)->format('j'),
                    'month' => Carbon::parse($rio->birth_date)->format('n'),
                    'year' => Carbon::parse($rio->birth_date)->format('Y'),
                ],
            ],
        ];

        return $accountInformation;
    }

    /**
     * Calculate the Application Fee Amount based on price.
     *
     * @param string|int $amount
     * @return string|int
     */
    public static function calculateApplicationFeeAmount($amount)
    {
        /** Application fee amount = 1.4% of the Amount */
        /** @var float */
        $appFeeAmount = (int)$amount * (1.4 / 100);

        return ((int) round((float)$appFeeAmount));
    }

    /**
     * Generate Stripe missing requirements.
     *
     * @param array $currentlyDues
     * @return array
     */
    public static function missingRequirements($currentlyDues)
    {
        $requirements = [];

        foreach ($currentlyDues as $due) {
            if (str_contains($due, StripeRequirementErrors::IDENTITY_DOCUMENT)) {
                $requirements[] = __('Identity Document');
            }
            if (str_contains($due, StripeRequirementErrors::BANK_ACCOUNT)) {
                $requirements[] = __('Bank account or debit card');
            }
            if (str_contains($due, StripeRequirementErrors::COMPANY_DIRECTORS)) {
                $requirements[] = __('Company directors');
            }
            if (str_contains($due, StripeRequirementErrors::COMPANY_NAME)) {
                $requirements[] = __('Business name');
            }
            if (str_contains($due, StripeRequirementErrors::COMPANY_KANA_NAME)) {
                $requirements[] = __('Kana business name');
            }
            if (str_contains($due, StripeRequirementErrors::COMPANY_KANJI_NAME)) {
                $requirements[] = __('Kanji business name');
            }
            if (str_contains($due, StripeRequirementErrors::COMPANY_PHONE)) {
                $requirements[] = __('Business phone number');
            }
            if (str_contains($due, StripeRequirementErrors::COMPANY_TAX_ID)) {
                $requirements[] = __('Business Tax ID');
            }
            if (str_contains($due, StripeRequirementErrors::DIRECTOR_DOB)) {
                $requirements[] = __("Director's date of birth");
            }
            if (str_contains($due, StripeRequirementErrors::DIRECTOR_FIRST_KANA)) {
                $requirements[] = __("Director's kana first name");
            }
            if (str_contains($due, StripeRequirementErrors::DIRECTOR_LAST_KANA)) {
                $requirements[] = __("Director's kana last name");
            }
            if (str_contains($due, StripeRequirementErrors::DIRECTOR_FIRST_KANJI)) {
                $requirements[] = __("Director's kanji first name");
            }
            if (str_contains($due, StripeRequirementErrors::DIRECTOR_LAST_KANJI)) {
                $requirements[] = __("Director's kanji last name");
            }
            if (str_contains($due, StripeRequirementErrors::JAPAN_RISA_COMPLIANCE)) {
                $requirements[] = __('Japan RISA compliance');
            }
            if (str_contains($due, StripeRequirementErrors::TERMS_OF_ACCEPTANCE)) {
                $requirements[] = __('Terms of service acceptance');
            }
            if (str_contains($due, StripeRequirementErrors::INDUSTRY)) {
                $requirements[] = __('Industry');
            }
            if (str_contains($due, StripeRequirementErrors::DESCRIPTION)) {
                $requirements[] = __('Product description');
            }
            if (str_contains($due, StripeRequirementErrors::WEBSITE)) {
                $requirements[] = __('Business website');
            }
            if (str_contains($due, StripeRequirementErrors::BUSINESS_PHONE)) {
                $requirements[] = __('Business support phone');
            }
            if (str_contains($due, StripeRequirementErrors::EMAIL)) {
                $requirements[] = __('Email Address');
            }
            if (str_contains($due, StripeRequirementErrors::OWNER)) {
                if (str_contains($due, StripeRequirementErrors::OWNER_KANA_ADDRESS)) {
                    $requirements[] = __("Owner's kana address");
                }
                if (str_contains($due, StripeRequirementErrors::OWNER_KANJI_ADDRESS)) {
                    $requirements[] = __("Owner's kanji address");
                }
                if (str_contains($due, StripeRequirementErrors::DOB)) {
                    $requirements[] = __("Owner's date of birth");
                }
                if (str_contains($due, StripeRequirementErrors::EMAIL)) {
                    $requirements[] = __("Owner's email");
                }
                if (str_contains($due, StripeRequirementErrors::PHONE)) {
                    $requirements[] = __("Owner's phone number");
                }
                if (str_contains($due, StripeRequirementErrors::FIRST_NAME_KANA)) {
                    $requirements[] = __("Owner's kana first name");
                }
                if (str_contains($due, StripeRequirementErrors::FIRST_NAME_KANJI)) {
                    $requirements[] = __("Owner's kanji first name");
                }
                if (str_contains($due, StripeRequirementErrors::LAST_NAME_KANA)) {
                    $requirements[] = __("Owner's kana last name");
                }
                if (str_contains($due, StripeRequirementErrors::LAST_NAME_KANJI)) {
                    $requirements[] = __("Owner's kanji last name");
                }
            }
        }

        if (count($requirements) > config('bphero.missing_requirements_max_count')) {
            $maxCount = (int)config('bphero.missing_requirements_max_count') - 1;
            $requirements = array_slice($requirements, 0, $maxCount);
            $requirements[] = __('Additional missing data');
        }

        return $requirements;
    }

    /**
     * Check if account id is valid
     *
     * @param \Stripe\Service\AccountService $accounts
     * @param string|null $id
     * @return bool
     */
    public static function isValidAccountId($accounts, $id)
    {
        // Guard clause for empty id
        if (empty($id)) {
            return false;
        }

        try {
            $accounts->retrieve($id);

            return true;
        } catch (PermissionException $exception) {
            return false;
        }
    }

    /**
     * Attach card payment method to a customer
     *
     * @param string $customerId
     * @return mixed
     */
    public function getStripeCustomer($customerId)
    {
        try {
            return $this->stripe->customers->retrieve(
                $customerId,
                []
            );
        } catch (\Exception $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Get all customer 's card payment method
     *
     * @param string $customerId
     * @return mixed
     */
    public function getCardPaymentMethod($customerId)
    {
        try {
            return $this->stripe->customers->allPaymentMethods(
                $customerId,
                ['type' => 'card']
            );
        } catch (\Exception $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Attach card payment method to a customer
     *
     * @param string $customerId
     * @param object $paymentMethod
     * @return \Stripe\PaymentMethod
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function attachCardPaymentMethod($customerId, $paymentMethod)
    {
        return $this->stripe->paymentMethods->attach(
            $paymentMethod->id,
            ['customer' => $customerId]
        );
    }

    /**
     * Create a setup intent for a customer
     *
     * @param string $customerId
     * @param object $paymentMethod
     * @return \Stripe\SetupIntent
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createCustomerSetupIntent($customerId, $paymentMethod)
    {
        return $this->stripe->setupIntents->create([
            'customer' => $customerId,
            'confirm' => true,
            'usage' => 'off_session',
            'payment_method' => $paymentMethod,
        ]);
    }

    /**
     * Detach card payment method to a customer
     *
     * @param array $paymentMethod
     * @return boolean|null
     */
    public function detachPaymentMethod($paymentMethod)
    {
        try {
            foreach ($paymentMethod as $card) {
                $this->stripe->paymentMethods->detach(
                    $card->id,
                    []
                );
            }

            return true;
        } catch (\Exception $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * Set payment method as default
     *
     * @param string $customerId
     * @param string $paymentMethodId
     * @return boolean|null
     */
    public function setCustomerDefaultPaymentMethod($customerId, $paymentMethodId)
    {
        try {
            $this->stripe->customers->update(
                $customerId,
                ['invoice_settings' => ['default_payment_method' => $paymentMethodId]]
            );

            return true;
        } catch (\Exception $exception) {
            report($exception);

            return false;
        }
    }
}
