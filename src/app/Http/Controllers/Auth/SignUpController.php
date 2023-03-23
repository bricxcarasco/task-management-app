<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupEmailVerificationRequest;
use App\Models\Rio;
use App\Providers\RouteServiceProvider;
use App\Services\SignupEmailVerificationService;
use Illuminate\Foundation\Auth\RegistersUsers;

class SignUpController extends Controller
{
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
     * User email signup page.
     *
     * @param mixed $referralCode
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    protected function email($referralCode = null)
    {
        // Fetch affiliate codes
        $affiliateCode = self::handleAffiliates(request());

        $user = auth()->user();

        if ($user) {
            return redirect()->route('home');
        }

        $rio = Rio::whereReferralCode($referralCode)->first();

        if ($referralCode && !$rio) {
            abort(404);
        }

        return view('auth.email_verification', compact('referralCode', 'affiliateCode'));
    }

    /**
     * validate and verify email signup.
     *
     * @param mixed $referralCode
     * @return \Illuminate\Contracts\Support\Renderable
     */
    protected function emailPost(SignupEmailVerificationRequest $request, $referralCode = null)
    {
        // Fetch affiliate codes
        $affiliateCode = self::handleAffiliates(request());
        $email = $request->input('email');

        $signupEmailService = new SignupEmailVerificationService($email);
        $signupEmailService->sendSignupEmailVerification($referralCode, $affiliateCode);

        return view('auth.email_verification_complete', compact('email'));
    }

    /**
     * Show landing page from Moshimo Affiliate.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse
     */
    public function landingPage()
    {
        // Fetch affiliate codes
        $affiliateCode = self::handleAffiliates(request());
        $user = auth()->user();

        if ($user) {
            return redirect()->route('home');
        }

        return view('auth.landing_page', compact('affiliateCode'));
    }

    /**
     * Handle affiliate codes
     *
     * @param mixed $requestData
     * @return mixed|null
     */
    public function handleAffiliates($requestData)
    {
        $affiliateCode = [];

        // Check Moshimo referral code
        if ($requestData->input('rd_code')) {
            $affiliateCode['moshimo'] = $requestData->input('rd_code');
        }

        // Check A8 referral code
        if ($requestData->input('a8')) {
            $affiliateCode['a8'] = $requestData->input('a8');
        }

        // Check if multiple referral code given
        if (count($affiliateCode) > 1) {
            abort(404);
        }

        return $affiliateCode;
    }
}
