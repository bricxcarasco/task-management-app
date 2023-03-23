<?php

/**
 * Workflow - ReactionTypes
 */

namespace App\Enums\Workflow;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Contracts\LocalizedEnum;

/**
 * App\Enums\Workflow\ReactionTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ReactionTypes extends Enum implements LocalizedEnum
{
    /**
     * Pending ReactionTypes
     *
     * @var int
     */
    public const PENDING =   1;

    /**
     * Approved ReactionTypes
     *
     * @var int
     */
    public const APPROVED =   2;

    /**
     * Returned ReactionTypes
     *
     * @var int
     */
    public const RETURNED = 3;

    /**
     * Rejected ReactionTypes
     *
     * @var int
     */
    public const REJECTED = 4;

    /**
     * Get reaction types without pendings
     *
     * @return array
     */
    public static function getReactionWithoutPending(): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $value) {
            if ($value !== self::PENDING) {
                $selectArray[$value] = static::getDescription($value);
            }
        }

        return $selectArray;
    }

    /**
     * Get reaction types without pendings
     *
     * @return array
     */
    public static function getReactionWithoutPendingForRule(): array
    {
        $array = static::asArray();
        $selectArray = [];

        foreach ($array as $value) {
            if ($value !== self::PENDING) {
                $selectArray[static::getDescription($value)] = $value;
            }
        }

        return $selectArray;
    }
}
