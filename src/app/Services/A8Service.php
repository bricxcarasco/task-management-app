<?php

namespace App\Services;

use App\Exceptions\A8Exception;
use Illuminate\Support\Facades\Log;

class A8Service
{
    /**
     * A8 API Host
     *
     * @var string
     */
    private $host;

    /**
     * A8 P_ID
     *
     * @var string|int
     */
    private $pId;

    /**
     * A8 Product Unit Price
     *
     * @var string|int
     */
    private $productUnitPrice;

    /**
     * A8 Product Quantity
     *
     * @var string|int
     */
    private $productQuantity;

    /**
     * A8 Amount
     *
     * @var string|int
     */
    private $amount;

    /**
     * A8 Product Code
     *
     * @var string|int
     */
    private $productCode;

    /**
     * A8 Product Unit Price
     *
     * @var string
     */
    private $currencyCode;

    /**
     * Constructor function
     *
     * @return void
     */
    public function __construct()
    {
        $this->host = config('a8.host');
        $this->pId = config('a8.p_id');
        $this->productUnitPrice = config('a8.product_unit_price');
        $this->productQuantity = config('a8.product_quantity');
        $this->amount = config('a8.amount');
        $this->productCode = config('a8.product_code');
        $this->currencyCode = config('a8.currency_code');

        if (empty($this->pId)) {
            throw new A8Exception('A8 p_id  is not set.', 500);
        }

        if (empty($this->currencyCode)) {
            throw new A8Exception('A8 currency code is not set.', 500);
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

        // Log Curl and response
        Log::info('OUTPUT CURL ' . $requestOptions[CURLOPT_URL]);
        Log::info('RESPONSE ' . $response);

        // Set response value
        if (curl_errno($ch)) {
            $response = curl_errno($ch);
        }
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $response;
    }

    /**
     * Notify A8 on successful registration
     *
     * @param string|int $userId
     * @param string $a8AffiliateCode
     * @return mixed
     */
    public function notifyA8($userId, $a8AffiliateCode)
    {
        // Set the url parameters
        $data = [
            'a8' => $a8AffiliateCode,
            'pid' => $this->pId,
            'so' => $userId,
            'si' => $this->productUnitPrice . '-' . $this->productQuantity . '-' . $this->amount . '-' . $this->productCode,
            'currency' => $this->currencyCode,
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
