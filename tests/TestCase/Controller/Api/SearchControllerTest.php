<?php
namespace App\Test\TestCase\Controller\Api;

use App\Controller\Api\SearchController;
use Cake\TestSuite\IntegrationTestTrait;
use App\Test\TestCase\Controller\Api\ApiTestCase;

/**
 * App\Controller\Api/SearchController Test Case
 *
 * @uses \App\Controller\Api/SearchController
 */
class SearchControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;
    public $fixtures = ['app.Users', 'app.Posts'];
    protected $requireToken = true;
    public $loggedInUser = 200002;

    public function testInitialization()
    {

    }
}
