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
class UsersControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;
    public $fixtures = ['app.Users', 'app.Followers'];
    public $loggedInUser = 200002;

    public function setUp()
    {
        // All actions here needs auth
        parent::setUp();
        $token = TokenGenerator::getToken();
        $this->addAuthorizationHeader($token);
    }

    public function testFetchFollowersWillReturnExactAmount()
    {
        $this->get('/api/users/activated/followers/count');
        $this->assertResponseOk();
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $count = $followersModel->find()
            ->where(['following_id' => $this->loggedInUser])
            ->count();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($count, $result->data->count);
    }

    public function testFetchFollowingWillReturnExactAmount()
    {
        $this->get('/api/users/activated/following/count');
        $this->assertResponseOk();
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $count = $followersModel->find()
            ->where(['user_id' => $this->loggedInUser])
            ->count();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($count, $result->data->count);
    }

    public function testFetchFollowsReturnExactAmount()
    {
        $this->get('/api/users/activated/follow/count');
        $this->assertResponseOk();
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $followerCount = $followersModel->find()
            ->where(['following_id' => $this->loggedInUser])
            ->count();
        $followingCount = $followersModel->find()
            ->where(['user_id' => $this->loggedInUser])
            ->count();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($followerCount, $result->data->followerCount);
        $this->assertEquals($followingCount, $result->data->followingCount);
    }

    public function testFetchRecommendedShouldNotBeFollowed()
    {
        $this->get('/api/users/follow/recommended');
        $this->assertResponseOk();
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $followedUsers = $followersModel->find()
            ->select('following_id')
            ->where(['user_id' => $this->loggedInUser])
            ->toArray();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertResponseNotContains(
            $followedUsers[0]->following_id,
            $result->data
        );
    }

    public function testFetchRecommendedShouldContainFriendsOfFriends()
    {
        $this->get('/api/users/follow/recommended');
        $this->assertResponseOk();
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $followedUsers = $followersModel->find()
            ->select('following_id')
            ->where(['user_id' => $this->loggedInUser])
            ->toArray();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertResponseContains(200012, $result->data);
        $this->assertResponseContains('anotherActivated', $result->data);
    }

    public function testUpdateProfile()
    {
        $data = [
            'username' => 'newUsernameha',
            'first_name' => 'Paolo Vincent',
            'last_name' => 'Julian',
            'birthdate' => '1994-07-12'
        ];
        $this->put('/api/users', $data);
        $this->assertResponseOk();
        $this->assertResponseContains('newUsernameha');
    }

    public function testUpdateProfileWPassword()
    {
        $data = [
            'username' => 'newUsernameha',
            'first_name' => 'Paolo Vincent',
            'last_name' => 'Julian',
            'birthdate' => '1994-07-12',
            'old_password' => 'qwe123',
            'password' => 'qwe123',
            'confirm_password' => 'qwe123',
        ];
        $this->put('/api/users', $data);
        $this->assertResponseOk();
        $this->assertResponseContains('newUsernameha');
    }
}
