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
class CommentOnPostsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts', 'app.Comments'];
    protected $requireToken = true;
    protected $loggedInUser = 200002;
    private $postsURL = '/api/posts';

    public function testEmptyFieldsShouldBodyError()
    {
        $data = ['body' => ''];
        $postId = 1;
        $this->post($this->postsURL . "/$postId/comments", $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testWhiteSpacesWillReturnError()
    {
        $data = ['body' => '  '];
        $postId = 1;
        $this->post($this->postsURL . "/$postId/comments", $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testNonExistingFieldShouldReturnErrorMessage()
    {
        $postId = 1;
        $this->post($this->postsURL . "/$postId/comments");
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testInvalidPostWillReturnNotFound()
    {
        $postId = 10932131;
        $this->post($this->postsURL . "/$postId/comments");
        $this->assertResponseCode(404);
    }

    public function testValidCreateWillReturnCommentCount()
    {
        $postId = 1;
        $data = ['body' => 'New Message!'];
        $this->post($this->postsURL . "/$postId/comments", $data);
        $this->assertResponseCode(201);
        $result = $this->getResponseData();
        $commentsTable = TableRegistry::getTableLocator()->get('Comments');
        $this->assertResponseContains('commentCount');
        $this->assertEquals($result->data->commentCount, $commentsTable->countPerPost($postId));
    }

    public function testFetchingCommentsOfPostWillReturnArray()
    {
        $postId = 1;
        $this->get($this->postsURL . "/$postId/comments?page=1");
        $this->assertResponseOk();
        $this->assertResponseContains('list');
        $this->assertResponseContains('This is a comment on post 1');
        $this->assertResponseContains('This is a comment on post 2');
        $this->assertResponseContains('existingusername');
    }

    public function testFetchingCommentsWillReturnTotalLeftAndCount()
    {
        $postId = 1;
        $page = 1;
        $this->get($this->postsURL . "/$postId/comments?page=$page");
        $this->assertResponseOk();
        $this->assertResponseContains('list');
        $this->assertResponseContains('totalCount');
        $commentsTable = TableRegistry::getTableLocator()->get('Comments');
        $totalCount = $commentsTable->countPerPost($postId);
        $result = $this->getResponseData();
        $this->assertEquals($result->data->totalCount, $totalCount);
    }
}
