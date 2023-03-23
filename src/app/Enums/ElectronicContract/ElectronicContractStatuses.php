<?php

/**
 * Electronic Contract Status
 */

namespace App\Enums\ElectronicContract;

use BenSampo\Enum\Enum;

/**
 * App\Enums\ElectronicContract\ElectronicContractStatuses
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class ElectronicContractStatuses extends Enum
{
    /**
     * Electronic Contract Status : Created
     *
     * @var int
     */
    public const CREATED = 0;

    /**
     * Electronic Contract Status : Prepared
     *
     * @var int
     */
    public const PREPARED = 1;
}
