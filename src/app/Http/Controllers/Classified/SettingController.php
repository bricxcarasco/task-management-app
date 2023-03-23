<?php

namespace App\Http\Controllers\Classified;

use App\Enums\Classified\CardPaymentStatus;
use App\Enums\Classified\StripeRequirementErrors;
use App\Enums\ServiceSelectionTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Classified\SettingRegistrationRequest;
use App\Models\ClassifiedSetting;
use App\Models\User;
use App\Objects\ServiceSelected;
use App\Services\StripeService;
use DB;
use Illuminate\Http\Request;
use Session;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\PermissionException;
use Stripe\StripeClient;

class SettingController extends Controller
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
     * Receiving account settings page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('view', [ClassifiedSetting::class, $request]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));
        $serviceName = $service->type === ServiceSelectionTypes::NEO
            ? $service->data->organization_name
            : $rio->full_name;

        // Get setting information
        $cardPayment = ClassifiedSetting::cardPaymentSetting()->first();
        $accountTransfer = ClassifiedSetting::accountTransferSetting()->first();
        $isSetAccountTransfer = !empty($accountTransfer);
        $cardPaymentStatus = CardPaymentStatus::NO_CARD_PAYMENT;
        $requirements = [];
        $stripeInfo = null;

        // Begin database transaction
        DB::beginTransaction();

        try {
            if (!empty($cardPayment)) {
                // Get stripe information
                $stripeInfo = StripeService::getStripeInfo($cardPayment);

                // Retrieve Stripe account information
                $account = $this->stripe->accounts->retrieve($stripeInfo['account_id'], []);
                /** @phpstan-ignore-next-line */
                $isPendingVerification = $account->requirements->disabled_reason;

                // Generate missing requirements from currently_due object
                /** @phpstan-ignore-next-line */
                $currentlyDue = $account->requirements->currently_due;
                if (!empty($currentlyDue)) {
                    $requirements = StripeService::missingRequirements($currentlyDue);
                    $cardPaymentStatus = CardPaymentStatus::RESTRICTED;
                }

                // Update pending status if already verified
                if (
                    $stripeInfo['is_pending'] &&
                    $isPendingVerification != StripeRequirementErrors::PENDING_VERIFICATION
                ) {
                    ClassifiedSetting::pendingStripeSetup(false);
                }

                if ($isPendingVerification == StripeRequirementErrors::PENDING_VERIFICATION) {
                    $cardPaymentStatus = CardPaymentStatus::PENDING;
                } elseif ($account->charges_enabled && $account->payouts_enabled) {
                    $cardPaymentStatus = CardPaymentStatus::COMPLETED;

                    if (!$stripeInfo['is_completed']) {
                        $stripeInfo['is_completed'] = true;

                        /** @var string */
                        $settingsByCard = json_encode($stripeInfo, JSON_FORCE_OBJECT);

                        // Update card payment setting
                        $cardPayment->update([
                            'settings_by_card' => $settingsByCard
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (PermissionException $exception) {
            report($exception);
            DB::rollBack();

            // Inject flag for revoked account
            $stripeInfo['is_revoked'] = true;
        } catch (ApiConnectionException $exception) {
            report($exception);
            DB::rollBack();

            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Problem connecting to Stripe'),
            ]);
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);
        }

        return view('classifieds.settings.index', compact(
            'rio',
            'service',
            'serviceName',
            'cardPaymentStatus',
            'isSetAccountTransfer',
            'requirements',
            'stripeInfo',
        ));
    }

    /**
     * Create Stripe Connect account and save card/Stripe payment setting.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveCardPaymentSetting(Request $request)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        // Get subject selected session
        $service = ServiceSelected::getSelected();

        // Get setting information
        /** @var ClassifiedSetting */
        $setting = ClassifiedSetting::setting()->first();

        DB::beginTransaction();

        try {
            // Get card payment setting information
            /** @var ClassifiedSetting */
            $cardSetting = ClassifiedSetting::cardPaymentSetting()->first();
            $accountId = null;

            // Get stripe information on existing card setting
            if (!empty($cardSetting)) {
                $stripeInfo = StripeService::getStripeInfo($cardSetting);
                $accountId = $stripeInfo['account_id'] ?? null;

                // Check if valid account id
                $isValidStripeAccount = StripeService::isValidAccountId($this->stripe->accounts, $accountId);

                // Set account id to null on invalid account
                if (!$isValidStripeAccount) {
                    $accountId = null;
                }

                // Throw bad request on completed valid account
                if ($isValidStripeAccount && $stripeInfo['is_completed']) {
                    abort(400);
                }
            }

            // Create new Stripe Connect account if no valid account yet
            if (empty($accountId)) {
                // Generate account information
                $accountInfo = StripeService::generateAccountInfo($service);

                // Create new account in Stripe Connect
                $account = $this->stripe->accounts->create($accountInfo);
                $accountId = $account->id;
            }

            // Create account link
            $accountLink = $this->stripe->accountLinks->create([
                'account' => $accountId,
                'refresh_url' => route('classifieds.settings.card-payment-failed'),
                'return_url' => route('classifieds.settings.card-payment-success'),
                'type' => 'account_onboarding',
            ]);

            /** @var string */
            $settingsByCard = json_encode(
                [
                    'account_id' => $accountId,
                    'is_pending' => false,
                    'is_completed' => false,
                ],
                JSON_FORCE_OBJECT
            );

            if (empty($setting)) {
                // Set service selected IDs
                $rioId = ServiceSelectionTypes::RIO === $service->type ? $service->data->id : null;
                $neoId = ServiceSelectionTypes::NEO === $service->type ? $service->data->id : null;

                // Register new setting
                $setting = new ClassifiedSetting();
                $setting->rio_id = $rioId;
                $setting->neo_id = $neoId;
                $setting->created_rio_id = $user->rio_id;
                $setting->settings_by_card = $settingsByCard;
                $setting->save();
            } else {
                // Update card payment setting
                $setting->update([
                    'settings_by_card' => $settingsByCard
                ]);
            }

            DB::commit();

            return redirect($accountLink->url);
        } catch (InvalidRequestException $exception) {
            report($exception);
            DB::rollBack();

            $errorMessage = $exception->getMessage();

            $stripeError = $exception->getStripeParam();

            switch ($stripeError) {
                case 'individual[phone]':
                    $errorMessage = __('Invalid Japan Phone Number');
                    break;
                case 'individual[dob][year]':
                    $errorMessage = __('Must be at least 13 years of age to use Stripe');
                    break;
                case 'business_profile[url]':
                    $errorMessage = __('Not a valid site url for neo');
                    break;
                default:
                    break;
            }

            return redirect()
                ->route('classifieds.settings.index')
                ->withAlertBox('danger', $errorMessage);
        } catch (ApiConnectionException $exception) {
            report($exception);
            DB::rollBack();

            return redirect()
                ->route('classifieds.settings.index')
                ->withAlertBox('danger', __('Problem connecting to Stripe'));
        } catch (\Exception $exception) {
            report($exception);
            DB::rollBack();

            return redirect()
                ->route('classifieds.settings.index')
                ->withAlertBox('danger', __('Issue encountered during Stripe setting'));
        }
    }

    /**
     * Stripe account - Successful account creation
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stripeAccountSetupSuccess()
    {
        // Get card payment setting information
        /** @var ClassifiedSetting */
        $cardSetting = ClassifiedSetting::cardPaymentSetting()->first();

        // Get Stripe account information from DB
        $stripeInfo = StripeService::getStripeInfo($cardSetting);

        // Get Stripe account information from Dashboard
        $account = $this->stripe->accounts->retrieve($stripeInfo['account_id'], []);
        /** @phpstan-ignore-next-line */
        $isPendingVerification = $account->requirements->disabled_reason;
        $isCompletedSetup = $account->charges_enabled && $account->payouts_enabled;

        if ($isPendingVerification == StripeRequirementErrors::PENDING_VERIFICATION) {
            ClassifiedSetting::pendingStripeSetup(true);

            return redirect()
                ->route('classifieds.settings.index')
                ->withAlertBox('success', __('Account under verification'));
        }

        if ($isCompletedSetup) {
            $stripeInfo['is_completed'] = $isCompletedSetup;
            $stripeInfo['is_pending'] = false;

            /** @var string */
            $settingsByCard = json_encode($stripeInfo, JSON_FORCE_OBJECT);

            // Update card payment setting
            $cardSetting->update([
                'settings_by_card' => $settingsByCard
            ]);

            return redirect()
                ->route('classifieds.settings.index')
                ->withAlertBox('success', __('Successfully created a Stripe account'));
        }

        return redirect()
            ->route('classifieds.settings.index')
            ->withAlertBox('danger', __('Incomplete Stripe setting'));
    }

    /**
     * Stripe account - Failed account creation
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stripeAccountSetupFailed()
    {
        return redirect()
            ->route('classifieds.settings.index')
            ->withAlertBox('danger', __('Account Link already expired'));
    }

    /**
     * Account transfer settings page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View|void
     */
    public function accountTransferSetting(Request $request)
    {
        $this->authorize('view', [ClassifiedSetting::class, $request]);

        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Rio */
        $rio = $user->rio;

        // Get subject selected session
        $service = json_decode(Session::get('ServiceSelected'));
        $serviceName = $service->type === ServiceSelectionTypes::NEO
            ? $service->data->organization_name
            : $rio->full_name;

        return view('classifieds.settings.account-transfer', compact(
            'service',
            'serviceName',
        ));
    }

    /**
     * Get bank account details
     *
     * @return mixed
     */
    public function getSettings()
    {
        $setting = ClassifiedSetting::getBankAccounts();

        return response()->respondSuccess($setting);
    }

    /**
     * Register bank account details
     *
     * @param \App\Http\Requests\Classified\SettingRegistrationRequest $request
     * @return mixed
     */
    public function registerSetting(SettingRegistrationRequest $request)
    {
        $requestData = $request->validated();

        $length = ClassifiedSetting::setting()->count();

        if ($length === config('bphero.max_bank_account')) {
            return response()->respondForbidden();
        }

        return response()->respondSuccess($requestData);
    }

    /**
     * Edit bank account details
     *
     * @param \App\Http\Requests\Classified\SettingRegistrationRequest $request
     * @return mixed
     */
    public function editSetting(SettingRegistrationRequest $request)
    {
        $requestData = $request->validated();

        return response()->respondSuccess($requestData);
    }

    /**
     * Save bank account details
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function saveAccountDetails(Request $request)
    {
        /** @var User */
        $user = auth()->user();
        $data = request()->all();

        //Get subject selected session
        $service = ServiceSelected::getSelected();

        //Check if account exist
        $isAccount = ClassifiedSetting::setting()->exists();

        $accounts = ClassifiedSetting::setting();
        $isExceed = count($data);
        $jsonArray = null;
        $arrayData = [];

        if ($isExceed > config('bphero.max_bank_account')) {
            return response()->respondForbidden();
        }

        if ($data) {
            foreach ($data as $account) {
                $settings_by_transfer['bank'] = $account['bank'];
                $settings_by_transfer['branch'] = $account['branch'];
                $settings_by_transfer['account_type'] = (int)$account['account_type'];
                $settings_by_transfer['account_number'] = $account['account_number'];
                $settings_by_transfer['account_name'] = $account['account_name'];
                $account['settings_by_transfer'] = json_encode($settings_by_transfer, JSON_FORCE_OBJECT);

                //Store in array
                $arrayData[] = $account['settings_by_transfer'];
            }

            $jsonArray = json_encode($arrayData, JSON_FORCE_OBJECT) ?: null;
        }

        try {
            DB::beginTransaction();

            if (!$isAccount) {
                $setting = new ClassifiedSetting();
                $setting->rio_id = $service->type === ServiceSelectionTypes::RIO ? $user->rio->id : null;
                $setting->neo_id = $service->type === ServiceSelectionTypes::NEO ? $service->data->id : null;
                $setting->created_rio_id = $user->rio->id;
                /** @phpstan-ignore-next-line */
                $setting->settings_by_transfer = $jsonArray;
                $setting->save();
            } else {
                $accounts->update([
                    'settings_by_transfer' => $jsonArray
                ]);
            }

            DB::commit();

            session()->put('alert', [
                'status' => 'success',
                'message' => __('Saved'),
            ]);

            return response()->respondSuccess();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            session()->put('alert', [
                'status' => 'danger',
                'message' => __('Server Error'),
            ]);

            return response()->respondInternalServerError();
        }
    }

    /**
     * Unset card payment
     *
     * @return \Illuminate\View\View|void
     */
    public function unsetCardPayment()
    {
        /** @var ClassifiedSetting */
        $setting = ClassifiedSetting::setting();

        $service = ServiceSelected::getSelected();

        if ($service->type === ServiceSelectionTypes::NEO && $service->data->is_member) {
            return redirect()
                ->back()
                ->withAlertBox('danger', __('Unauthorized'));
        }

        if ($setting->exists()) {
            $setting->update([
                'settings_by_card' => null,
            ]);
        }

        return redirect()
            ->back()
            ->withAlertBox('success', __('Stripe account has been unset successfully'));
    }
}
