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
    protected $tobeFollowedId = 200013;
    protected $tobeUnfollowedId = 200014;
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
        $this->assertResponseContains(0);
    }

    public function testFollowUserWillReturn200()
    {
        $this->post('/api/users/' . $this->tobeFollowedId . '/follow');
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $doesExists = $followersModel->exists([
            'following_id' => $this->tobeFollowedId,
            'user_id' => $this->loggedInUser
        ]);
        $this->assertEquals($doesExists, true);
        $this->assertResponseOk();
    }

    public function testUnFollowUserWillDeleteDataFromDB()
    {
        $this->post('/api/users/' . $this->tobeUnfollowedId . '/follow');
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $doesExists = $followersModel->exists([
            'following_id' => $this->tobeUnfollowedId,
            'user_id' => $this->loggedInUser
        ]);
        $this->assertEquals($doesExists, false);
        $this->assertResponseOk();
    }

    public function testFetchFollowersOfUserWillReturnSuccess()
    {
        $this->get('/api/users/' . $this->loggedInUser . '/followers');
        $this->assertResponseOk();
        // Check if contains a user that follows the given user
        $this->assertResponseContains(200001);
    }

    public function testFetchFollowingWillReturnSuccess()
    {
        $this->get('/api/users/' . $this->loggedInUser . '/following');
        $this->assertResponseOk();
        // Check if contains the user being followed by the given user
        $this->assertResponseContains(200001);
    }
}
