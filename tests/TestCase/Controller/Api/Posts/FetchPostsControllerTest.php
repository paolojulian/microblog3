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

    public $fixtures = ['app.Users', 'app.Posts', 'app.Followers', 'app.Likes', 'app.Comments'];
    protected $requireToken = true;
    protected $loggedInUser = 200002;
    protected $username = 'activated';

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

    public function testFetchUsersPostsShouldDisplayPostsBySelf()
    {
        $this->get('/api/posts/users/' . $this->username);
        $this->assertResponseOk();
        $this->assertResponseContains($this->loggedInUser);
    }

    public function testFetchingSinglePostWillReturnPostEntityWUser()
    {
        $postId = 1;
        $this->get('/api/posts/' . $postId);
        $this->assertResponseOk();
        $this->assertResponseContains($postId);
        $this->assertResponseContains('Lorem Ipsum');
        $this->assertResponseContains(200002);
        $this->assertResponseContains('activated');
    }

    public function testFetchingSharedPostWillRetrieveOriginalPost()
    {
        $sharedPostId = 4;
        $originalPostId = 1;
        $this->get('/api/posts/' . $sharedPostId);
        $this->assertResponseOk();
        $this->assertResponseContains($sharedPostId);
        $this->assertResponseContains($originalPostId);
        $this->assertResponseContains('Lorem Ipsum');
        $this->assertResponseContains('This is a shared post');
    }

    public function testFetchingPostIncludesLikersUserId()
    {
        $postId = 1;
        $this->get('/api/posts/' . $postId);
        $this->assertResponseOk();
        $this->assertResponseContains('Lorem Ipsum');
        $this->assertResponseContains('likes');
        $this->assertResponseContains(200001);
        $this->assertResponseContains(200002);
    }

    public function testFetchingPostIncludesCommentCount()
    {
        $postId = 1;
        $this->get('/api/posts/' . $postId);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertResponseContains('Lorem Ipsum');
        $this->assertResponseContains('comments');
        $this->assertEquals($result->data->post->comments, 2);
    }
}
