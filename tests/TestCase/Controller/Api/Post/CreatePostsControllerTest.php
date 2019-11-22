<?php
namespace App\Test\TestCase\Controller\Api\Post;

use App\Controller\Api\Post\PostsController;
use App\Test\TestCase\Controller\Api\ApiTestCase;
use App\Test\Utils\TokenGenerator;
use Cake\TestSuite\IntegrationTestTrait;


/**
 * App\Controller\Api/Post/PostsController Test Case
 *
 * @uses \App\Controller\Api/Post/PostsController
 */
class CreatePostsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts'];
    public $loggedInUser = 200002;

    public function setUp()
    {
        // All actions here needs auth
        parent::setUp();
        $token = TokenGenerator::getToken();
        $this->addAuthorizationHeader($token);
    }

    public function testEmptyFieldsShouldBodyError()
    {
        // Title and img is optional
        $data = [
            'title' => '',
            'body' => ''
        ];
        $this->post('/api/post/create', $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testNonExistingBodyShouldReturnError()
    {
        $this->post('/api/post/create');
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testSpacesWillReturnError()
    {
        $data = [
            'title' => '',
            'body' => '  '
        ];
        $this->post('/api/post/create');
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testValidPostWillReturnPostObject()
    {
        $data = [
            'title' => '',
            'body' => 'New Body'
        ];
        $this->post('/api/post/create', $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($data['body'], $result->data->body);
        $this->assertEquals($this->loggedInUser, $result->data->user_id);
    }

    public function testValidPostWithWhiteSpacesWillTrimBody()
    {
        $data = [
            'title' => '',
            'body' => '  This has white spaces  '
        ];
        $this->post('/api/post/create', $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(trim($data['body']), $result->data->body);
    }
}
