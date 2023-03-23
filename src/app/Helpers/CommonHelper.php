<?php

/**
 * Common Helper
 *
 * @author yns
 */

namespace App\Helpers;

use App\Enums\EntityType;
use App\Models\Neo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * App\Helpers\CommonHelper
 *
 * Common reusable functions that could be use throughout the system
 *
 * @package AgrigoSystem
 * @subpackage PhpDocumentor
 */
class CommonHelper
{
    /**
     * Convert encoding to UTF-8
     *
     * @param string $value - value to convert
     * @return array|string|false
     */
    public static function convertEncodingToUtf8($value)
    {
        return mb_convert_encoding($value, 'UTF-8');
    }

    /**
     * Checks array key-value pair for values or should be required
     *
     * @param array $requiredKeys   indexed array
     * @param array $data           associative array to be checked
     * @return bool
     */
    public static function checkRequiredArrayKeys($requiredKeys, $data)
    {
        foreach ($requiredKeys as $requiredKey) {
            if (!isset($data[$requiredKey]) || is_null($data[$requiredKey])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if array is associative array
     *
     * @param array $data  array to be checked
     * @return bool
     */
    public static function isAssoc(array $data)
    {
        if ([] === $data) {
            return false;
        }

        return array_keys($data) !== range(0, count($data) - 1);
    }

    /**
     * Pluck associative array from given key array
     *
     * @param array $data  array to be plucked
     * @param array $keys  keys to be used for plucking
     * @return array
     */
    public static function pluckArray(array $data, array $keys)
    {
        return array_intersect_key($data, array_flip($keys));
    }

    /**
     * Checks if database transaction already exists
     *
     * @return bool
     */
    public static function isInitialDatabaseTransaction()
    {
        return DB::connection(DB::getDefaultConnection())->transactionLevel() === 0;
    }

    /**
     * Get basename from path
     *
     * Custom basename() due to unexpected results from PHP method
     *
     * @param string $path path to process
     *
     * @return string
     */
    public static function getBasename(string $path)
    {
        $temp = explode(DIRECTORY_SEPARATOR, $path);

        return end($temp);
    }

    /**
     * Get filename from basename
     *
     * Custom pathinfo() due to unexpected results from PHP method
     *
     * @param string $basename path to process
     *
     * @return string|null
     */
    public static function getFilename(string $basename)
    {
        return preg_replace('/\.[\w]{3,5}$/', '', $basename);
    }

    /**
     * Checks if string is JSON
     *
     * @param string $string
     * @return bool
     */
    public static function isJson($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Create random number
     *
     * @param int $length
     * @return int
     */
    public static function randomNumber($length = 16)
    {
        /** @var int */
        $maxValue = (10 ** $length) - 1;

        return random_int(0, $maxValue);
    }

    /**
     * Create random numeric code
     *
     * @param int $length
     * @return string
     */
    public static function randomNumbericCode($length = 16)
    {
        $number = strval(self::randomNumber($length));

        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Generate random alphanumeric string
     *
     * @param int $length
     * @return string
     */
    public static function randomAlphaNumericString($length = 16)
    {
        return Str::random($length);
    }

    /**
     * Create OTP
     *
     * @param array $options
     * @return array
     */
    public static function createOTP($options = [])
    {
        // Initialize default
        $expiry = $options['expiry'] ?? 600;
        $length = $options['length'] ?? 4;

        // Create numeric code
        $code = self::randomNumbericCode($length);

        // Prepare expiration date
        $expirationDate = now()->addSeconds($expiry)->format('c');

        // Encrypt information
        $result = Crypt::encryptString($expirationDate . '|' . $code);

        return [
            'identifier' => $result,
            'code' => $code,
            'expiration' => $expirationDate,
        ];
    }

    /**
     * Verify OTP
     *
     * @param string $identifier
     * @param string $code
     * @return bool
     */
    public static function verifyOTP($identifier, $code)
    {
        try {
            // Decrypt information
            $decrypted = Crypt::decryptString($identifier);
            $temp = explode('|', $decrypted);

            // Get expiration
            $expiration = Carbon::parse($temp[0]);

            // Check if expired
            if ($expiration->lessThanOrEqualTo(now())) {
                return false;
            }

            // Check if code matches encrypted
            return ($code === $temp[1]);
        } catch (\Exception $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * Format price without rounding off decimal places
     *
     * @param string $price. Price to format
     * @return string|int
     */
    public static function priceFormat($price)
    {
        // Guard clause for null price
        if (empty($price)) {
            return 0;
        }

        // Convert to whole number
        $decimalVal = explode('.', $price, 2)[1] ?? '00';
        if ($decimalVal === '00') {
            /** @phpstan-ignore-next-line */
            return number_format($price);
        }

        /** @phpstan-ignore-next-line */
        return number_format(floor($price * 100) / 100, 2);
    }

    /**
     * Removes main directory / bucket name in file path
     * for Storage facade usage
     *
     * @param string $path
     *
     * @return string
     */
    public static function removeMainDirectoryPath($path)
    {
        $pos = strpos($path, '/');

        if ($pos !== false) {
            return substr_replace($path, '', 0, $pos + 1);
        }

        return $path;
    }

    /**
     * Url encodes path
     *
     * Only encodes each path and does not include slashes
     *
     * @param string $path
     *
     * @return string
     */
    public static function urlencodePath($path)
    {
        $temp = explode('/', $path);

        $encoded = array_map(function ($directory) {
            return rawurlencode($directory);
        }, $temp);

        return implode('/', $encoded);
    }

    /**
     * Identifies the type of entity passed
     *
     * @param \App\Models\Rio|\App\Models\Neo $entity
     * @return int
     */
    public static function getEntityType($entity)
    {
        return ($entity instanceof Neo)
            ? EntityType::NEO
            : EntityType::RIO;
    }

    /**
     * Convert to bytes
     *
     * @param string|null $unit
     * @param int $value
     *
     * @return int
     */
    public static function convertToBytes($unit, $value)
    {
        switch ($unit) {
            case 'GB':
                return (int) config('bphero.gb_to_bytes') * $value;
            default:
                return (int) $value;
        }
    }
}
