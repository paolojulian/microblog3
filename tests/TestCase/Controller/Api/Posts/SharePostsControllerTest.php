<?php
namespace App\Test\TestCase\Controller\Api\Posts;

use App\Controller\Api\Posts\PostsController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use App\Test\Utils\TokenGenerator;
use Cake\TestSuite\IntegrationTestTrait;


/**
 * App\Controller\Api/Post/PostsController Test Case
 *
 * @uses \App\Controller\Api/Post/PostsController
 */
class SharePostsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts'];
    public $loggedInUser = 200002;
    protected $requireToken = true;

    public function testemptyfieldsshouldnotreturnerror()
    {
        // body is optional
        $data = [
            'body' => ''
        ];
        $this->post('/api/posts/share/1', $data);
        $this->assertresponsecode(201);
    }

    public function testNonExistingBodyShouldNotReturnError()
    {
        $this->post('/api/posts/share/1');
        $this->assertResponseCode(201);
    }

    public function testNonExistingPostShould404()
    {
        $this->post('/api/posts/share/31321321');
        $this->assertResponseCode(404);
    }

    public function testNotIntegerPostShouldBeForbidden()
    {
        $this->post('/api/posts/share/qweq');
        $this->assertResponseCode(403);
    }

    public function testSharingPostWithLongBodyWillReturnError()
    {
        $data = [
            'body' => 'dsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkddsakldjaslkdjjjjjjjjjjjjjjdsakldjaslkdj'
        ];
        $this->post('/api/posts/share/1', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Maximum of 140 characters is allowed');
    }
}
