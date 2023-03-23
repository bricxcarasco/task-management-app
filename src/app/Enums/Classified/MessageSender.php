<?php

/**
 * Message Sender
 */

namespace App\Enums\Classified;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\MessageSender
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class MessageSender extends Enum implements LocalizedEnum
{
    /**
     * Message sent by Seller
     *
     * @var int
     */
    public const SELLER = 0;

    /**
     * Message sent by Buyer
     *
     * @var int
     */
    public const BUYER = 1;
}
