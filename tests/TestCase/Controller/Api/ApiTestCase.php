<?php
namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use App\Test\Utils\TokenGenerator;

/**
 * App\Controller\Api/AuthsController Test Case
 *
 * @uses \App\Controller\Api/AuthsController
 */
class ApiTestCase extends TestCase
{
    protected $requireToken = false;
    protected $loggedInUser = 200002;
    protected $requestHeaders = [
        'Accept' => 'application/json',
        'Content-Type' => 'x-www-form-urlencoded'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->configRequest([
            'headers' => $this->requestHeaders
        ]);
        if ($this->requireToken) {
            $token = TokenGenerator::getToken($this->loggedInUser);
            $this->addAuthorizationHeader($token);
        }
    }

    protected function addAuthorizationHeader(string $token)
    {
        $this->requestHeaders['Authorization'] = 'Bearer ' . $token;
        $this->configRequest([
            'headers' => $this->requestHeaders
        ]);
    }
}