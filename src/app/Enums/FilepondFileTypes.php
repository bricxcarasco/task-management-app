<?php

/**
 * OTP Type Constants
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\FilepondFileTypes
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class FilepondFileTypes extends Enum
{
    /**
     * Limbo Files - Temporarily Uploaded files
     *
     * @var int
     */
    public const LIMBO = 'upload_file';

    /**
     * Local Files - Already uploaded in server
     *
     * @var int
     */
    public const LOCAL = 'local_file';
}
