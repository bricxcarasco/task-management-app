<?php

namespace App\Services;

use App\Enums\DevicePlatforms;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;

class CordovaService
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
     * Check if device is android
     *
     * @return bool
     */
    public static function isAndroid()
    {
        // Get cookie
        /** @var string */
        $cookieKey = config('cordova.platform_type_key');

        return Cookie::get($cookieKey) === DevicePlatforms::ANDROID;
    }

    /**
     * Check if device is ios
     *
     * @return bool
     */
    public static function isIos()
    {
        // Get cookie
        /** @var string */
        $cookieKey = config('cordova.platform_type_key');

        return Cookie::get($cookieKey) === DevicePlatforms::IOS;
    }

    /**
     * Inject core cordova assets to web system
     *
     * @return string|null
     */
    public static function injectCoreAssets()
    {
        // Initialize variables
        $cookieKey = config('cordova.platform_type_key');
        /** @var string */
        $platform = Cookie::get($cookieKey);
        $targetPlatforms = [
            DevicePlatforms::ANDROID,
            DevicePlatforms::IOS,
        ];

        // Disregard other platforms
        if (!in_array($platform, $targetPlatforms)) {
            return null;
        }

        // Get plugins
        $plugins = self::getPlugins($platform);

        // Prepare view platform
        $platform = strtolower($platform);

        // Prepare view data
        $viewData = compact(
            'platform',
            'plugins'
        );

        /** @var string */
        return view('layouts/components/cordova_assets', $viewData)
            ->render();
    }

    /**
     * Check if device is ios
     *
     * @param string $platform
     * @return array
     */
    public static function getPlugins($platform)
    {
        $plugins = config('cordova.plugins', []);

        return $plugins[$platform] ?? [];
    }

    /**
     * Verify platform key
     *
     * @return bool
     */
    public static function verifyToken()
    {
        // Get cookie
        $cookieKey = config('cordova.platform_key');

        /** @var string */
        $cookieValue = Cookie::get($cookieKey);

        if (empty($cookieValue)) {
            return false;
        }

        try {
            // Process cookie value
            $prefix = CookieValuePrefix::create($cookieKey, Crypt::getKey());
            $decryptedValue = Crypt::decrypt($cookieValue, false);
            $hasValidPrefix = strpos($decryptedValue, $prefix) === 0;

            if (!$hasValidPrefix) {
                return false;
            }

            // Process decrypted value
            $processedValue = CookieValuePrefix::remove($decryptedValue);
            $temp = explode('_', $processedValue);

            return ($temp[0] === config('cordova.platform_token'));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Set platform cookie
     *
     * @param string $platform
     * @return void
     */
    public static function setCookie($platform)
    {
        try {
            // Get configuration
            $tokenKey = config('cordova.platform_key');
            $typeKey = config('cordova.platform_type_key');
            $token = config('cordova.platform_token');
            $expiration = config('cordova.cookie_expiration');

            // Generate cookie value
            $prefix = CookieValuePrefix::create($tokenKey, Crypt::getKey());
            $generatedValue = $prefix . $token . '_' . $platform;
            $cookieValue = Crypt::encrypt($generatedValue, false);

            // Set cookie
            Cookie::queue($tokenKey, $cookieValue, $expiration, null, null, null, true);
            Cookie::queue($typeKey, $platform, $expiration, null, null, null, true);
        } catch (\Exception $exception) {
            report($exception);
        }
    }
}
