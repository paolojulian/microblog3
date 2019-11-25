<?php
namespace App\Test\TestCase\Controller\Api\Posts;

use App\Controller\Api\Posts\PostsController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use App\Test\Utils\TokenGenerator;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Api/Posts/PostsController Test Case
 *
 * @uses \App\Controller\Api/Posts/PostsController
 */
class FetchPostsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts', 'app.Followers'];
    public $loggedInUser = 200002;

    public function setUp()
    {
        // All actions here needs auth
        parent::setUp();
        $token = TokenGenerator::getToken();
        $this->addAuthorizationHeader($token);
    }

    public function testFetchPostsShouldNotDisplayNotFollowedUsersPost()
    {
        $this->get('/api/posts/');
        $this->assertResponseOk();
        $followersModel = TableRegistry::getTableLocator()->get('Followers');
        $notFollowedUsers = $followersModel->find()
            ->select('following_id')
            ->where(['user_id !=' => $this->loggedInUser])
            ->toArray();
        $this->assertResponseNotContains(200012);
    }

    public function testFetchPostsShouldDisplayPostsByFollowedUser()
    {
        $this->get('/api/posts/');
        $this->assertResponseOk();
        $this->assertResponseContains(200001);
    }

    public function testFetchPostsShouldDisplayPostsBySelf()
    {
        $this->get('/api/posts/');
        $this->assertResponseOk();
        $this->assertResponseContains($this->loggedInUser);
    }
}
