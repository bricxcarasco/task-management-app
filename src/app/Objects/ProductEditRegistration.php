<?php

namespace App\Objects;

use Session;

class ProductEditRegistration
{
    /**
     * Session key in
     *
     * @var string
     */
    public static $sessionKey = 'productEditRegistration';

    /**
     * Get product registration session
     *
     * @return object
     */
    public static function getSession()
    {
        // Return empty if non-existing session
        if (!Session::has('productEditRegistration')) {
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
     * @param mixed $product
     * @return void
     */
    public static function setSession($entity, $product)
    {
        Session::put(self::$sessionKey, json_encode(
            [
                'data' => $entity,
                'id' => $product->id
            ]
        ));
    }
}
