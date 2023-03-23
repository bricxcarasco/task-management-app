<?php

namespace App\Http\Controllers\Auth;

use App\Enums\OTPTypes;
use App\Events\RegistrationVerifiedEvent;
use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationInputRequest;
use App\Http\Requests\SignupEmailVerifiedRequest;
use App\Models\Rio;
use App\Models\UserVerification;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Enums\AffiliateTypes;
use App\Enums\Rio\SecretQuestionType;
use App\Enums\PrefectureTypes;
use App\Exceptions\CmComException;
use App\Http\Requests\Auth\RegistrationCompleteRequest;
use App\Models\User;
use App\Services\A8Service;
use App\Services\CmComService;
use App\Services\MoshimoService;
use Exception;
use Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
    }

    /**
     * Confirm page upon user registration.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function confirm(RegistrationInputRequest $request)
    {
        $request->merge([
            'referral_code' => $this->generateReferralCode(),
        ]);
        $input_rio = $request->registerRioAttributes();
        $input_user = $request->registerUserAttributes();
        $input_rio_profile = $request->registerRioProfileAttributes();
        $otherFields = $request->registerOtherAttributes();

        $request->session()->put('registration.users.input', [
            'rio' => $input_rio,
            'user' => $input_user,
            'rio_profile' => $input_rio_profile,
            'other' => $otherFields,
            'registration_url' => $request->headers->get('referer'),
        ]);

        return view('auth.register_confirm', compact('input_rio', 'input_user', 'input_rio_profile'));
    }

    /**
     * Send SMS Authenticate OTP
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function smsAuthenticateSendOtp(Request $request)
    {
        // Get registration input and request data
        $requestData = $request->all();
        $registrationInput = $request->session()->get('registration.users.input');

        // Initialize variables
        $phoneNumber = $registrationInput['rio']['tel'] ?? null;
        $registrationRoute = $registrationInput['registration_url']
            ?? route('registration.email');
        $otpType = $requestData['type'] ?? OTPTypes::SMS;

        try {
            // Handle empty phone number
            if (empty($phoneNumber)) {
                throw new Exception('Phone number for OTP sending is not set.', 400);
            }

            // Check if verify OTP should be skipped
            $isBypassed = config('bphero.sms_auth_bypass_flag');

            // Generate SMS Auth OTP
            $smsAuthIdentifier = Str::random();
            if (!$isBypassed) {
                $cmcomService = new CmComService();

                // Check phone number if valid via CMCOM
                $isValidNumber = $cmcomService->validateNumber($phoneNumber);

                if (!$isValidNumber) {
                    throw new Exception('Phone number for OTP sending is invalid.');
                }

                // Generate OTP and fetch identifier code
                if ((int) $otpType === OTPTypes::VOICE) {
                    $smsAuthIdentifier = $cmcomService->generateVoiceOTP($phoneNumber, [
                        'length' => config('bphero.sms_auth_code_length'),
                        'expiry' => config('bphero.sms_auth_code_expiry'),
                        'intro-prompt' => __('messages.voice_auth_code_intro_prompt'),
                        'code-prompt' => __('messages.voice_auth_code_code_prompt'),
                        'outro-prompt' => __('messages.voice_auth_code_outro_prompt'),
                    ]);
                } else {
                    $smsAuthIdentifier = $cmcomService->generateSmsOTP($phoneNumber, [
                        'length' => config('bphero.sms_auth_code_length'),
                        'expiry' => config('bphero.sms_auth_code_expiry'),
                        'message' => __('messages.sms_auth_code_text_message'),
                    ]);
                }
            }

            // Handle non-existing OTP identifier
            if (empty($smsAuthIdentifier)) {
                throw new CmComException('SMS Authentication OTP sending failed.');
            }

            // Attach SMS authentication identifier
            $request->session()->put('registration.users.input.sms_authentication_identifier', $smsAuthIdentifier);
            $request->session()->put('registration.users.input.sms_authentication_type', $otpType);
        } catch (CmComException $exception) {
            report($exception);

            if (!empty($requestData['resend'])) {
                return redirect()
                    ->route('registration.sms.index')
                    ->withAlertBox('danger', __('messages.sms_auth_code_send_failed'));
            }

            return redirect($registrationRoute)
                ->withAlertBox('danger', __('messages.sms_auth_code_send_failed'))
                ->withInput(array_merge(
                    $registrationInput['rio'] ?? [],
                    $registrationInput['user'] ?? [],
                    $registrationInput['rio_profile'] ?? []
                ));
        } catch (\Exception $exception) {
            report($exception);

            return redirect($registrationRoute)
                ->withAlertBox('danger', __('messages.sms_auth_code_send_failed'))
                ->withInput(array_merge(
                    $registrationInput['rio'] ?? [],
                    $registrationInput['user'] ?? [],
                    $registrationInput['rio_profile'] ?? []
                ));
        }

        // Prepare response
        $response = redirect()->route('registration.sms.index');

        // Display alert when resend generation
        if (!empty($requestData['resend'])) {
            $response->withAlertBox('success', __('messages.sms_auth_code_resend_successful'));
        }

        return $response;
    }

    /**
     * Shows SMS Authentication form page
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function smsAuthenticateForm(Request $request)
    {
        // Get registration input
        $registrationInput = $request->session()->get('registration.users.input');

        // Initialize variables
        $identifier = $registrationInput['sms_authentication_identifier'] ?? null;
        $otpType = $registrationInput['sms_authentication_type'] ?? null;
        $phoneNumber = $registrationInput['rio']['tel'] ?? ' - ';
        $registrationRoute = route('registration.email');

        // Redirect to previous pages when no identifier is found
        if (empty($identifier)) {
            return redirect($registrationRoute);
        }

        return view('auth.sms_authentication')->with(compact(
            'phoneNumber',
            'identifier',
            'otpType',
        ));
    }

    /**
     * Successfully register the user.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function complete(RegistrationCompleteRequest $request)
    {
        if (!$request->session()->has('registration.users.input')) {
            return redirect()->back();
        }

        $registrationInput = $request->session()->get('registration.users.input');

        // Prepare registration inputs and request data
        $requestData = $request->validated();

        // Check if verify OTP should be skipped
        $isBypassed = config('bphero.sms_auth_bypass_flag');

        if (!$isBypassed) {
            // Verify SMS Authentication code
            $cmcomService = new CmComService();
            $isVerified = $cmcomService->verifyOTP($requestData['identifier'], $requestData['code'], $requestData['otp_type']);

            // Redirect back when unable to verify
            if (!$isVerified) {
                return redirect()
                    ->route('registration.sms.index')
                    ->withAlertBox('danger', __('messages.sms_auth_code_verify_failed'));
            }
        }

        DB::transaction(function () use ($registrationInput) {
            $user = Rio::createRioUser($registrationInput);

            /** @var object */
            $userVerification = UserVerification::whereEmail($user->email)
                ->latest()
                ->first();

            if ($userVerification->referral_rio_id) {
                Rio::whereId($user->id)->update([
                    'referral_rio_id' => $userVerification->referral_rio_id,
                ]);
            }
            UserVerification::deleteUserVerificationByEmail($user->email);
            event(new RegistrationVerifiedEvent($user));

            // Manage affilliates
            switch ($registrationInput['user']['affiliate']) {
                case AffiliateTypes::MOSHIMO:
                    $moshimoService = new MoshimoService();
                    $sendNotif = $moshimoService->notifyMoshimo($user->id, $registrationInput['other']['rd_code']);
                    break;
                case AffiliateTypes::A8:
                    $a8Service = new A8Service();
                    $sendNotif = $a8Service->notifyA8($user->id, $registrationInput['other']['a8']);
                    break;
                default:
                    break;
            }
        });

        // Clear registration session
        $request->session()->remove('registration.users.input');

        return view('auth.register_complete', ['email' => $registrationInput['user']['email']]);
    }

    /**
     * Verify url link then proceed to registration page.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function verifyUrl(SignupEmailVerifiedRequest $request)
    {
        $user = auth()->user();

        if ($user) {
            return redirect()->route('home');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email:filter|max:255|exists:user_verifications,email',
            'token' => 'required|string|exists:user_verifications,token|max:' . Constant::RANDOM_HASH_CHARACTERS,
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        $now = Carbon::now();
        $email = $request->email;
        $token = $request->token;
        $user_verify = UserVerification::where('email', $email)->where('token', $token)->firstOrFail();

        if ($now->gt($user_verify->expiration_datetime)) {
            return redirect()
                ->route('registration.email')
                ->withAlertBox('danger', __('Signup Email Verification Expired'));
        }

        $prefectures = PrefectureTypes::getValues();
        $secret_questions = SecretQuestionType::getValues();
        $affiliateCode = [
            'moshimo' => $user_verify->affi_moshimo_code,
            'a8' => $user_verify->affi_a8_code,
        ];

        return view('auth.register', compact('email', 'token', 'prefectures', 'secret_questions', 'affiliateCode'));
    }

    /**
     * Generate random referral code.
     *
     * @return string
     */
    public function generateReferralCode()
    {
        do {
            $referralCode = Str::random(config('bphero.referral_code_length'));
        } while (Rio::whereReferralCode($referralCode)->exists());

        return $referralCode;
    }
}
