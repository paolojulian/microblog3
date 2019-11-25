<?php
namespace App\Test\TestCase\Controller\Api\Posts;

use App\Controller\Api\Posts\PostsController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\ORM\TableRegistry;

/**
 * App\Controller\Api/Post/PostsController Test Case
 *
 * @uses \App\Controller\Api/Post/PostsController
 */
class LikesPostsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts'];
    protected $requireToken = true;
    protected $loggedInUser = 200002;

    private $postId = 1;
    private $likePostURL = "/api/posts/like/";

    public function testLikingPostWillToggleStatusInDB()
    {
        $this->patch($this->likePostURL . $this->postId);
        $this->assertResponseCode(200);
        $likesModel = TableRegistry::getTableLocator()->get('Likes');
        $doesExists = $likesModel->exists([
            'post_id' => $this->postId,
            'user_id' => $this->loggedInUser
        ]);
        $this->assertEquals($doesExists, true);
    }
}
