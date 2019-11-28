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
class DeleteCommentsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;

    public $fixtures = ['app.Users', 'app.Posts', 'app.Comments'];
    protected $requireToken = true;
    protected $loggedInUser = 200002;
    private $postsURL = '/api/posts';
}
