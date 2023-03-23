<?php

/**
 * Forms operation types
 */

namespace App\Enums\Form;

use BenSampo\Enum\Enum;

/**
 * App\Enums\Form\OperationTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class OperationTypes extends Enum
{
    /**
     * Operation type - Duplicate
     *
     * @var int
     */
    public const DUPLICATE = 'duplicate';
}
