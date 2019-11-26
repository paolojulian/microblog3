<?php
namespace App\Test\TestCase\Controller\Api\Users;

use App\Controller\Api\Users\UsersController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use App\Test\Utils\TokenGenerator;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Api/Users/UsersController Test Case
 *
 * @uses \App\Controller\Api/Users/UsersController
 */
class FetchUsersControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;
    public $fixtures = ['app.Users', 'app.Followers'];
    protected $requireToken = true;
    protected $loggedInUser = 200002;
    protected $username = 'activated';

    public function testFetchProfileWillReturnUserEntity()
    {
        $this->get('/api/users/' . $this->username);
        $this->assertResponseOk();
        $this->assertResponseContains($this->loggedInUser);
    }
}
