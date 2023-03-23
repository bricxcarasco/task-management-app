<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;

class FcmService
{
    /**
     * Check if has cookie
     *
     * @return bool
     */
    public static function hasCookie()
    {
        // Get cookie
        /** @var string */
        $cookieKey = config('cordova.platform_key');

        return Cookie::has($cookieKey);
    }

    /**
     * Set fcm cookie token
     *
     * @param int $userId
     * @param string $token
     * @return void
     */
    public static function setCookie($userId, $token)
    {
        try {
            // Get configuration
            $key = config('fcm.cookie_key');
            $expiration = config('fcm.cookie_expiration');

            // Generate cookie value
            $prefix = CookieValuePrefix::create($key, Crypt::getKey());
            $generatedValue = $prefix . $token . '|' . $userId;
            $cookieValue = Crypt::encrypt($generatedValue, false);

            // Set cookie
            Cookie::queue($key, $cookieValue, $expiration, null, null, null, true);
        } catch (\Exception $exception) {
            report($exception);
        }
    }

    /**
     * Deregister token from cookie
     *
     * @return void
     */
    public static function deregisterCookieToken()
    {
        // Get cookie
        $cookieKey = config('fcm.cookie_key');

        /** @var string */
        $cookieValue = Cookie::get($cookieKey);

        if (empty($cookieValue)) {
            return;
        }

        try {
            // Process cookie value
            $prefix = CookieValuePrefix::create($cookieKey, Crypt::getKey());
            $decryptedValue = Crypt::decrypt($cookieValue, false);
            $hasValidPrefix = strpos($decryptedValue, $prefix) === 0;

            if (!$hasValidPrefix) {
                return;
            }

            // Process decrypted value
            $processedValue = CookieValuePrefix::remove($decryptedValue);
            $temp = explode('|', $processedValue);

            // Fetch user
            $user = User::findOrFail($temp[1]);

            // Remove token from user
            $user->removeFcmToken($temp[0]);

            // Remove cookie
            Cookie::queue(Cookie::forget($cookieKey));
        } catch (\Exception $e) {
            return;
        }
    }
}
