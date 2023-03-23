<?php

namespace App\Services;

use App\Events\SignupEmailVerificationEvent;
use App\Helpers\Constant;
use App\Models\Rio;
use App\Models\UserVerification;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SignupEmailVerificationService
{
    /**
     * @var string
     */
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * Send register email that verification or verified.
     *
     * @param mixed $referralCode
     * @param mixed $affiliateCode
     * @return void
     */
    public function sendSignupEmailVerification($referralCode, $affiliateCode)
    {
        $user_verify = $this->create($this->email, $referralCode, $affiliateCode);
        event(new SignupEmailVerificationEvent($user_verify));
    }

    /**
     * Create a new user verification before registration.
     *
     * @param mixed $email
     * @param mixed $referralCode
     * @param mixed $affiliateCode
     * @return \App\Models\UserVerification
     */
    private function create($email, $referralCode, $affiliateCode)
    {
        /** @var object */
        $rio = Rio::whereReferralCode($referralCode)->first();

        return UserVerification::create([
            'email' => $email,
            'referral_rio_id' => $rio->id ?? null,
            'token' => Str::random(Constant::RANDOM_HASH_CHARACTERS),
            'expiration_datetime' => Carbon::now()->addDay(),
            'affi_moshimo_code' => $affiliateCode['moshimo'] ?? null,
            'affi_a8_code' => $affiliateCode['a8'] ?? null,
        ]);
    }
}
