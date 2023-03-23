<?php

namespace App\Services\CmCom;

use App\Exceptions\CmComException;
use CURLFile;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SignService extends \App\Services\CmComService
{
    /**
     * CM.com API Host
     *
     * @var string
     */
    private $signServiceHost;

    /**
     * CM.com Message Gateway - API Key
     *
     * @var string
     */
    private $signServiceApiKey;

    /**
     * CM.com Voice API - API Key
     *
     * @var string
     */
    private $signServiceSecret;

    /**
     * Constructor function
     *
     * @return void
     */
    public function __construct()
    {
        $this->signServiceHost = config('cmcom.sign_service_host');
        if (config('cmcom.sign_env') === 'production') {
            $this->signServiceApiKey = config('cmcom.sign_service_api_key');
            $this->signServiceSecret = config('cmcom.sign_service_secret');
        } else {
            $this->signServiceHost = config('cmcom.sign_service_host_sandbox');
            $this->signServiceApiKey = config('cmcom.sign_service_api_key_sandbox');
            $this->signServiceSecret = config('cmcom.sign_service_secret_sandbox');
        }

        if (empty($this->signServiceApiKey)) {
            throw new CmComException('No CM Sign service API key set.', 500);
        }

        if (empty($this->signServiceSecret)) {
            throw new CmComException('No CM Sign service Secret set.', 500);
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
        return $this->signServiceHost . $path;
    }

    /**
     * Send curl request to CM.com API
     *
     * @param array $options
     * @return mixed
     */
    private function sendCmSignRequest($options)
    {
        // Intialize default options
        $default = [
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

        return $response;
    }

    /**
     * Generate Voice OTP
     *
     * @param array $options other possible JWT payloads
     * @return string|null
     */
    private function generateCmSignJWT($options = [])
    {
        try {
            $key = $this->signServiceSecret;

            //JWT token default configurations
            /** @var int $currentDateTime */
            $currentDateTime = strtotime(now()->format('Y-m-d H:i:s'));
            $defaultPayload = [
                "iat" => $currentDateTime,
                "nbf" => $currentDateTime,
                "exp" => strtotime(config('bphero.cmcom_sign_jwt_expiration_duration'), $currentDateTime)
            ];

            $payload = $defaultPayload + $options;

            $encodedToken = JWT::encode($payload, $key, 'HS256', $this->signServiceApiKey);

            return $encodedToken;
        } catch (\Exception $exception) {
            report($exception);

            return null;
        }
    }

    /**
     * Upload a document to CM Sign
     *
     * @param string $filePath filePath of the file you wish to upload
     * @return array|null
     */
    public function uploadDocument($filePath)
    {
        // Bypass create dossier API call
        if (config('bphero.cmcom_sign_bypass_flag')) {
            return [
                'id' => 'document-' . now()->format('YmdHis'),
            ];
        }

        //generate JWT token
        $token = $this->generateCmSignJWT();

        // Prepare post fields
        $postFields = [];
        if (!empty($filePath)) {
            $postFields = [
                'file' => new CURLFile($filePath),
            ];
        }

        // Prepare options
        $requestOptions = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: multipart/form-data',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_POST => true,
            CURLOPT_URL => $this->getEndpoint('/sign/v1/upload'),
            CURLOPT_POSTFIELDS => $postFields,
        ];

        // Send request
        $result = $this->sendCmSignRequest($requestOptions);

        $response = json_decode($result, true);

        // Handle non-successful upload
        if (empty($response['id'])) {
            Log::error('CM Sign Service - Upload Document Failed', [$response]);
        }

        return $response ?? null;
    }

    /**
     * Create Dossier in CM Sign Service
     *
     * @param string $documentId
     * @param array $data
     * @return array|null
     */
    public function createDossier($documentId, $data)
    {
        // Bypass create dossier API call
        if (config('bphero.cmcom_sign_bypass_flag')) {
            return [
                'id' => 'dossier-' . now()->format('YmdHis'),
                'prepareUrl' => 'https://www.cm.com/',
                'invitees' => [
                    [
                        'id' => 'invitee-' . now()->format('YmdHis'),
                    ],
                ],
            ];
        }

        // Generate JWT token
        $token = $this->generateCmSignJWT();

        // Prepare post fields
        $postFields = [
            'name' => $data['contract_name'],
            'prepare' => true,
            'locale' => config('cmcom.sign_service.locale'),
            'files' => [
                [
                    'id' => $documentId,
                ],
            ],
            'owners' => [
                [
                    'name' => $data['sender_name'],
                    'email' => $data['sender_email'],
                ]
            ],
            'invitees' => [
                [
                    'name' => $data['recipient_name'],
                    'email' => $data['recipient_email'],
                    'position' => 1
                ]
            ],
        ];

        // Prepare options
        $requestOptions = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_POST => true,
            CURLOPT_URL => $this->getEndpoint('/sign/v1/dossiers'),
            CURLOPT_POSTFIELDS => json_encode($postFields),
        ];

        // Send request
        $result = $this->sendCmSignRequest($requestOptions);

        $response = json_decode($result, true);

        // Handle non-successful upload
        if (empty($response['id'])) {
            Log::error('CM Sign Service - Create Dossier Failed', [$response]);
        }

        return $response ?? null;
    }

    /**
     * Fetch all webhooks registered
     *
     * @return array|null
     */
    public function fetchWebhooks()
    {
        // Generate JWT token
        $token = $this->generateCmSignJWT();

        // Prepare options
        $requestOptions = [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_URL => $this->getEndpoint("/sign/v1/clients/{$this->signServiceApiKey}/webhooks"),
        ];

        // Send request
        $result = $this->sendCmSignRequest($requestOptions);

        $response = json_decode($result, true);

        return $response ?? null;
    }

    /**
     * Subscribe to CM Sign service event
     *
     * @param string|null $url
     * @return array|null
     */
    public function subscribeEvent($url = null)
    {
        // Generate JWT token
        $token = $this->generateCmSignJWT();

        // Prepare post fields
        $postFields = [
            'url' => $url ?? route('cm-sign.update-contract-status'),
            'events' => [
                'dossier.prepared',
            ],
            'headers' => [
                'cm-sign-webhook-key' => static::generateWebhookSignature(),
            ],
        ];

        // Prepare options
        $requestOptions = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_POST => true,
            CURLOPT_URL => $this->getEndpoint("/sign/v1/clients/{$this->signServiceApiKey}/webhooks"),
            CURLOPT_POSTFIELDS => json_encode($postFields),
        ];

        // Send request
        $result = $this->sendCmSignRequest($requestOptions);

        $response = json_decode($result, true);

        // Handle non-successful upload
        if (empty($response['id'])) {
            Log::error('CM Sign Service - Subscribe Failed', [$response]);
        }

        return $response ?? null;
    }

    /**
     * Unsubscribe to CM Sign service event
     *
     * @param string $webhookId
     * @return array|null
     */
    public function unsubscribeEvent($webhookId)
    {
        // Generate JWT token
        $token = $this->generateCmSignJWT();

        // Prepare options
        $requestOptions = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_URL => $this->getEndpoint("/sign/v1/clients/{$this->signServiceApiKey}/webhooks/{$webhookId}"),
        ];

        // Send request
        $result = $this->sendCmSignRequest($requestOptions);

        $response = json_decode($result, true);

        // Handle non-successful upload
        if (empty($response['id'])) {
            Log::error('CM Sign Service - Unsubscribe Failed', [$response]);
        }

        return $response ?? null;
    }

    /**
     * Send invites to recipient
     *
     * @param string $dossierId
     * @param string $inviteeId
     * @return array|null
     */
    public function sendInvite($dossierId, $inviteeId)
    {
        // Bypass create dossier API call
        if (config('bphero.cmcom_sign_bypass_flag')) {
            return [
                [
                    'id' => 'invite-' . now()->format('YmdHis'),
                ],
            ];
        }

        // Generate JWT token
        $token = $this->generateCmSignJWT();

        // Prepare post fields
        $postFields = [
            [
                'inviteeId' => $inviteeId,
                'channel' => 'email',
                'expiresIn' => config('bphero.cmcom_sign_contract_sign_expiration'),
            ],
        ];

        // Prepare options
        $requestOptions = [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_POST => true,
            CURLOPT_URL => $this->getEndpoint("/sign/v1/dossiers/{$dossierId}/invites"),
            CURLOPT_POSTFIELDS => json_encode($postFields),
        ];

        // Send request
        $result = $this->sendCmSignRequest($requestOptions);

        $response = json_decode($result, true);

        // Handle non-successful upload
        if (!isset($response[0]['id'])) {
            Log::error('CM Sign Service - Send invite failed', [$response]);
        }

        return $response ?? null;
    }

    /**
     * generates exclusive key to be used by the CM sign webhooks
     *
     * @return string
     */
    public static function generateWebhookSignature()
    {
        $key = config('cmcom.sign_webhook_key');

        return Crypt::encryptString($key);
    }

    /**
     * Verifies if request from CM sign is authorized
     *
     * @param string $requesterKey
     * @return bool
     */
    public static function isValidWebhookSignature($requesterKey)
    {
        try {
            $key = config('cmcom.sign_webhook_key');

            return Crypt::decryptString($requesterKey) === $key;
        } catch (\Exception $exception) {
            return false;
        }
    }
}
