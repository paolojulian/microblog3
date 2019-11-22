<?php
namespace App\Test\TestCase\Controller\Api\User;

use App\Controller\Api\User\UsersController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use App\Test\Utils\TokenGenerator;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Api/User/UsersController Test Case
 *
 * @uses \App\Controller\Api/User/UsersController
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
        $this->get('/api/user/activated/followers/count');
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
        $this->get('/api/user/activated/following/count');
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
        $this->get('/api/user/activated/follow/count');
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
        $this->get('/api/user/activated/follow/recommended');
        $this->assertResponseOk();
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $followedUsers = $followersModel->find()
            ->select('following_id')
            ->where(['user_id' => $this->loggedInUser])
            ->toArray();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertResponseNotContains(
            $followedUsers[0]->following_id,
            $result->data->users
        );
        $this->assertResponseContains(200012, $result->data->users);
        $this->assertResponseContains('anotherActivated', $result->data->users);
    }
}
