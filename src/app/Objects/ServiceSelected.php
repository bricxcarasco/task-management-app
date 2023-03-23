<?php

namespace App\Objects;

use App\Enums\ServiceSelectionTypes;
use App\Exceptions\ServiceSessionNotFoundException;
use App\Models\Neo;
use App\Models\Rio;
use App\Models\User;
use App\Services\PlanSubscriptionService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Session;

class ServiceSelected
{
    /**
     * Session key in
     *
     * @var string
     */
    public static $sessionKey = 'ServiceSelected';

    /**
     * Get service selection selected
     *
     * @return object
     */
    public static function getSelected()
    {
        /** @var User */
        $user = auth()->user();

        /** @var Rio */
        $rio = $user->rio;

        // Return empty if non-existing rio
        if (empty($rio)) {
            throw new ServiceSessionNotFoundException();
        }

        // Get service selected session
        $subject = Session::get(self::$sessionKey);

        // Return default subject
        if (empty($subject)) {
            return (object) [
                'data' => $rio,
                'type' => ServiceSelectionTypes::RIO,
                'plan_subscriptions' => PlanSubscriptionService::generateSessionData($rio),
                'storage_info' => PlanSubscriptionService::getStorageInfo($rio),
            ];
        }

        return json_decode($subject);
    }

    /**
     * Set service selection session
     *
     * @param \App\Models\Neo|\App\Models\Rio $entity
     * @return void
     */
    public static function setSelected($entity)
    {
        // Set cookie
        self::setCookie($entity);

        Session::put(self::$sessionKey, json_encode(
            [
                'data' => $entity,
                'type' => ($entity instanceof Rio)
                    ? ServiceSelectionTypes::RIO
                    : ServiceSelectionTypes::NEO,
                'plan_subscriptions' => PlanSubscriptionService::generateSessionData($entity),
                'storage_info' => PlanSubscriptionService::getStorageInfo($entity),
            ]
        ));
    }

    /**
     * Set service selection cookie
     *
     * @param \App\Models\Neo|\App\Models\Rio $entity
     * @return void
     */
    public static function setCookie($entity)
    {
        try {
            // Identify entity type
            $entityType = ($entity instanceof Rio)
                ? ServiceSelectionTypes::RIO
                : ServiceSelectionTypes::NEO;

            // Get configuration
            $cookieKey = config('bphero.service_selected_cookie_key');
            $cookieExpiration = config('bphero.service_selected_cookie_expiration');

            // Generate cookie value
            $prefix = CookieValuePrefix::create($cookieKey, Crypt::getKey());
            $generatedValue = $prefix . $entityType . '_' . $entity->id;
            $cookieValue = Crypt::encrypt($generatedValue, false);

            // Set cookie
            Cookie::queue($cookieKey, $cookieValue, $cookieExpiration, null, null, null, true);
        } catch (\Exception $exception) {
            report($exception);
        }
    }

    /**
     * Get service selection cookie
     *
     * @return object|null
     */
    public static function getCookie()
    {
        // Get cookie
        $cookieKey = config('bphero.service_selected_cookie_key');
        /** @var string */
        $cookieValue = Cookie::get($cookieKey);

        if (empty($cookieValue)) {
            return null;
        }

        try {
            // Process cookie value
            $prefix = CookieValuePrefix::create($cookieKey, Crypt::getKey());
            $decryptedValue = Crypt::decrypt($cookieValue, false);
            $hasValidPrefix = strpos($decryptedValue, $prefix) === 0;

            if (!$hasValidPrefix) {
                return null;
            }

            // Process decrypted value
            $processedValue = CookieValuePrefix::remove($decryptedValue);
            $temp = explode('_', $processedValue);

            if ($temp[0] === ServiceSelectionTypes::NEO) {
                $entity = Neo::find($temp[1]);
            } else {
                $entity = Rio::find($temp[1]);
            }

            // Initialize service data
            $service = [
                'data' => (object) [
                    'id' => $temp[1],
                ],
                'type' => $temp[0],
            ];

            // Fetch actual entity data
            if (!empty($entity)) {
                $service = array_merge($service, [
                    'data' => $entity,
                    'plan_subscriptions' => PlanSubscriptionService::generateSessionData($entity),
                    'storage_info' => PlanSubscriptionService::getStorageInfo($entity),
                ]);
            }

            return (object) $service;
        } catch (DecryptException $e) {
            return null;
        }
    }
}
