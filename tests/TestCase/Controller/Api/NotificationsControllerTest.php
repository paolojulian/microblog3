<?php
namespace App\Test\TestCase\Controller\Api;

use App\Controller\Api\SearchController;
use Cake\TestSuite\IntegrationTestTrait;
use App\Test\TestCase\Controller\Api\ApiTestCase;

/**
 * App\Controller\Api/SearchController Test Case
 *
 * @uses \App\Controller\Api/SearchController
 */
class NotificationsControllerTest extends ApiTestCase
{
    use IntegrationTestTrait;
    public $fixtures = ['app.Users', 'app.Notifications'];
    protected $requireToken = true;
    public $loggedInUser = 200002;

    public function testFetchUnreadNotificationsShouldReturnOk()
    {
        $this->get('/api/notifications/unread?notifications[page]=1');
        $this->assertResponseOk();
    }

    public function testFetchUnreadNotificationsShouldContainUnreadMessagesOnly()
    {
        $this->get('/api/notifications/unread?notifications[page]=1');
        $this->assertResponseOk();

        $this->assertResponseContains('Chefpipz liked your post');

        $this->assertResponseNotContains('This is a read post');
        $this->assertResponseNotContains('Another post');
    }

    public function testOutOfBoundsPageWillReturnEmpty()
    {
        $this->get('/api/notifications/unread?notifications[page]=100');
        $this->assertResponseOk();
        $this->assertResponseContains('status');
        $this->assertResponseContains(200);
    }
}
