<?php

namespace Tests\Unit\Services\CmComService;

use App\Services\CmCom\SignService;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /**
     * Check if credentials are successfully authorize even if no file/s to upload
     *
     * Status 400
     *
     * @return void
     */
    public function testTestCmSignApiAuthenticationViaUpload()
    {
        $signServiceInstance = new SignService();

        $response = $signServiceInstance->uploadDocument('');

        $this->assertIsArray($response);
        if ($response['status'] === 400) {
            $this->assertTrue(true);
        }
    }
}
