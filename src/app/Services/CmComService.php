<?php

namespace App\Services;

use App\Enums\OTPTypes;
use App\Exceptions\CmComException;
use App\Helpers\CommonHelper;

class CmComService
{
    /**
     * CM.com API Host
     *
     * @var string
     */
    private $host;

    /**
     * CM.com Message Gateway - API Key
     *
     * @var string
     */
    private $messageApiKey;

    /**
     * CM.com Voice API - API Key
     *
     * @var string
     */
    private $voiceApiKey;

    /**
     * Constructor function
     *
     * @return void
     */
    public function __construct()
    {
        $this->host = config('cmcom.host');
        $this->messageApiKey = config('cmcom.message_api_key');
        $this->voiceApiKey = config('cmcom.voice_api_key');

        if (empty($this->messageApiKey)) {
            throw new CmComException('No CM service Message API key set.', 500);
        }

        if (empty($this->voiceApiKey)) {
            throw new CmComException('No CM service Voice API key set.', 500);
        }
    }

    /**
     * Get API Endpoint URL
     *
     * @param string $path
     * @return string
     */
    private function getEndpoint($path)
    {
        return $this->host . $path;
    }

    /**
     * Send curl request to CM.com API
     *
     * @param array $options
     * @return mixed
     */
    private function sendRequest($options)
    {
        // Intialize default options
        $default = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-CM-ProductToken: ' . $this->messageApiKey,
            ],
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true
        ];

        // Merge set custom to default options
        $requestOptions = $default + $options;

        // Curl request to API
        $ch = curl_init();
        curl_setopt_array($ch, $requestOptions);

        /** @var string */
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }

    /**
     * Generate OTP
     *
     * @param string $phoneNumber
     * @param array $options
     * @return string|null
     */
    public function generateSmsOTP($phoneNumber, $options = [])
    {
        // Initialize default post inputs
        $default = [
            'recipient' => $phoneNumber,
            'sender' => config('cmcom.message_gateway_sender'),
        ];

        // Prepare post fields
        $postFields = array_merge($default, $options);

        // Prepare options
        $requestOptions = [
            CURLOPT_URL => $this->getEndpoint('/v1.0/otp/generate'),
            CURLOPT_POSTFIELDS => json_encode($postFields),
        ];

        // Send request
        $result = $this->sendRequest($requestOptions);

        return $result->id ?? null;
    }

    /**
     * Verify OTP
     *
     * @param string $identifier
     * @param string $code
     * @param int $type
     * @return bool
     */
    public function verifyOTP($identifier, $code, $type = OTPTypes::SMS)
    {
        // Handle voice OTP
        if ((int) $type === OTPTypes::VOICE) {
            return CommonHelper::verifyOTP($identifier, $code);
        }

        // Prepare post inputs
        $postFields = [
            'id' => $identifier,
            'code' => $code,
        ];

        // Prepare options
        $options = [
            CURLOPT_URL => $this->getEndpoint('/v1.0/otp/verify'),
            CURLOPT_POSTFIELDS => json_encode($postFields),
        ];

        // Send request
        $result = $this->sendRequest($options);

        return $result->valid ?? false;
    }

    /**
     * Validate phone number
     *
     * @param string $phoneNumber
     * @return bool
     */
    public function validateNumber($phoneNumber)
    {
        // Prepare post inputs
        $postFields = [
            'phonenumber' => $phoneNumber,
            'mnp_lookup' => true,
        ];

        // Prepare options
        $options = [
            CURLOPT_URL => $this->getEndpoint('/v1.1/numbervalidation'),
            CURLOPT_POSTFIELDS => json_encode($postFields),
        ];

        // Send request
        $result = $this->sendRequest($options);

        return $result->valid_number ?? false;
    }

    /**
     * Generate Voice OTP
     *
     * @param string $phoneNumber
     * @param array $options
     * @return string|null
     */
    public function generateVoiceOTP($phoneNumber, $options = [])
    {
        try {
            // Create OTP
            $otp = CommonHelper::createOTP([
                'expiry' => $options['expiry'] ?? 600,
                'length' => $options['length'] ?? 4,
            ]);

            // Get caller number
            $callerNumber = config('cmcom.voice_api.caller');

            if (empty($callerNumber)) {
                throw new CmComException('No Voice API caller number set.');
            }

            // Initialize default post inputs
            $default = [
                'callee' => $phoneNumber,
                'caller' => config('cmcom.voice_api.caller'),
                'anonymous' => config('cmcom.voice_api.anonymous'),
                'intro-prompt-type' => 'TTS',
                'code-prompt-type' => 'TTS',
                'code' => $otp['code'],
                'max-replays' => config('cmcom.voice_api.max_replays'),
                'auto-replay' => config('cmcom.voice_api.auto_replay'),
                'voice' => [
                    'language' => config('cmcom.voice_api.operator.language'),
                    'gender' => config('cmcom.voice_api.operator.gender'),
                    'number' => config('cmcom.voice_api.operator.version'),
                    'volume' => config('cmcom.voice_api.operator.volume')
                ],
            ];

            // Prepare post fields
            $postFields = array_merge($default, $options);

            // Prepare options
            $requestOptions = [
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-CM-ProductToken: ' . $this->voiceApiKey,
                ],
                CURLOPT_URL => $this->getEndpoint('/voiceapi/v2/OTP'),
                CURLOPT_POSTFIELDS => json_encode($postFields),
            ];

            // Send request
            $result = $this->sendRequest($requestOptions);

            // Prepare results
            $isSuccess = $result->success ?? false;
            $identifier = $otp['identifier'] ?? null;

            return $isSuccess ? $identifier : null;
        } catch (\Exception $exception) {
            report($exception);

            return null;
        }
    }
}
