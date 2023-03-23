<?php

namespace App\Objects;

use Session;

class ProductRegistration
{
    /**
     * Session key in
     *
     * @var string
     */
    public static $sessionKey = 'productRegistration';

    /**
     * Get product registration session
     *
     * @return object
     */
    public static function getSession()
    {
        // Return empty if non-existing session
        if (!Session::has('productRegistration')) {
            return response()->respondNotFound();
        }

        // Get product registration session
        $subject = Session::get(self::$sessionKey);

        return json_decode($subject);
    }

    /**
     * Set product registration session
     *
     * @param mixed $entity
     * @return void
     */
    public static function setSession($entity)
    {
        Session::put(self::$sessionKey, json_encode($entity));
    }
}
