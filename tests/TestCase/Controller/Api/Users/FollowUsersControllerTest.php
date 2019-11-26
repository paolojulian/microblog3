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
class FollowUsersControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;
    public $fixtures = ['app.Users', 'app.Followers'];
    protected $requireToken = true;
    protected $loggedInUser = 200002;
    protected $username = 'activated';
    protected $followedUsername = 'existingusername';
    protected $notFollowedUsername = 'anotherActivated';

    public function testFetchingIsFollowedWillReturnCorrectValue()
    {
        $this->get('/api/users/' . $this->followedUsername . '/is-following');
        $this->assertResponseOk();
        $this->assertResponseContains(1);
    }

    public function testFetchingNotFollowedUserWillReturnNot()
    {
        $this->get('/api/users/' . $this->notFollowedUsername . '/is-following');
        $this->assertResponseOk();
        $this->assertResponseContains(-1);
    }
}
