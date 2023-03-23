<?php

namespace App\Services;

use App\Exceptions\MoshimoException;

class MoshimoService
{
    /**
     * Moshimo API Host
     *
     * @var string
     */
    private $host;

    /**
     * Moshimo P_ID
     *
     * @var string|int
     */
    private $moshimoPId;

    /**
     * Moshimo PC_ID
     *
     * @var string|int
     */
    private $moshimoPcId;

    /**
     * Constructor function
     *
     * @return void
     */
    public function __construct()
    {
        $this->host = config('moshimo.host');
        $this->moshimoPId = config('moshimo.moshimo_p_id');
        $this->moshimoPcId = config('moshimo.moshimo_pc_id');

        if (empty($this->moshimoPId)) {
            throw new MoshimoException('Moshimo p_id  is not set.', 500);
        }

        if (empty($this->moshimoPcId)) {
            throw new MoshimoException('Moshimo pc_id is not set.', 500);
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
     * Send curl request to Moshimo API
     *
     * @param array $options
     * @return mixed
     */
    private function sendRequest($options)
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

        // Set response value
        if (curl_errno($ch)) {
            $response = curl_errno($ch);
        }
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $response;
    }

    /**
     * Notify Moshimo on successful registration
     *
     * @param string|int $userId
     * @param string $moshimoAffiliateCode
     * @return mixed
     */
    public function notifyMoshimo($userId, $moshimoAffiliateCode)
    {
        // Set the url parameters
        $data = [
            'p_id' => $this->moshimoPId,
            'pc_id' => $this->moshimoPcId,
            'm_v' => $userId,
            'rd_code' => $moshimoAffiliateCode,
        ];

        $urlParams = http_build_query($data);

        // Prepare options
        $requestOptions = [
            CURLOPT_URL => $this->getEndpoint($urlParams),
        ];

        // Send request
        return $this->sendRequest($requestOptions);
    }
}
