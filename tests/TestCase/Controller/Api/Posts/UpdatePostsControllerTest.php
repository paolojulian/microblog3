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
class UpdatePostsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts'];
    public $loggedInUser = 200002;
    private $updatedPostId = 1;
    private $updatePostURL = "/api/posts/update/1";

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
        $this->post($this->updatePostURL, $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testNotAvailableFieldsShouldReturnError()
    {
        $this->post($this->updatePostURL);
        $this->assertResponseCode(422);
        $this->assertResponseContains('body');
        $this->assertResponseContains('Please enter your message');
    }

    public function testMaxInputShouldInvalidate()
    {
        $data = [
            'title' => 'WERTYUIODASDSADJASLKDJSAKLDJLSAKJDKLASJDKLASJDKLASJDKLSA',
            'body' => 'WERTYUIODASDSADJASLKDJSAKLDJLSAKJDKLASJDKLASJDKLASJDKLSAWERTYUIODASDSADJASLKDJSAKLDJLSAKJDKLASJDKLASJDKLASJDKLSAWERTYUIODASDSADJASLKDJSAKLDJLSAKJDKLASJDKLASJDKLASJDKLSA'
        ];
        $this->post($this->updatePostURL, $data);
        $this->assertResponseCode(422);
        $this->assertResponseContains('title');
        $this->assertResponseContains('body');
        $this->assertResponseContains('Maximum of 30 characters is allowed');
        $this->assertResponseContains('Maximum of 140 characters is allowed');
    }

    public function testSuccessUpdateShouldReturnPostEntity()
    {
        $data = [
            'title' => 'Updated Post',
            'body' => 'Updated Body'
        ];
        $this->post($this->updatePostURL, $data);
        $this->assertResponseCode(200);
        $this->assertResponseContains('Updated Post');
        $this->assertResponseContains('Updated Body');
    }
}
