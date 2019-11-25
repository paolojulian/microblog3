<?php
namespace App\Test\TestCase\Controller\Api\Posts;

use App\Controller\Api\Posts\PostsController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use App\Test\Utils\TokenGenerator;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Api/Post/PostsController Test Case
 *
 * @uses \App\Controller\Api/Post/PostsController
 */
class DeletePostsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts'];
    public $loggedInUser = 200002;
    private $deletePostId = 1;
    private $deletePostURL = "/api/posts/";

    public function setUp()
    {
        // All actions here needs auth
        parent::setUp();
        $token = TokenGenerator::getToken();
        $this->addAuthorizationHeader($token);
    }

    public function testCannotDeleteOtherUsersPost()
    {
        $this->delete($this->deletePostURL . 2);
        $this->assertResponseCode(403);
    }

    public function testDeleteOwnPostWillRemoveFromDB()
    {
        $this->delete($this->deletePostURL . $this->deletePostId);
        $postsModel = TableRegistry::getTableLocator()->get('Posts');
        $doesExists = $postsModel->exists(['id' => $this->deletePostId]);
        $this->assertResponseCode(204);
        $this->assertEquals($doesExists, false);
    }
}
