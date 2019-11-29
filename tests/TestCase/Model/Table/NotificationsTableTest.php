<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NotificationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NotificationsTable Test Case
 */
class NotificationsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\NotificationsTable
     */
    public $Notifications;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Notifications',
        'app.Users',
        'app.Posts',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Notifications') ? [] : ['className' => NotificationsTable::class];
        $this->Notifications = TableRegistry::getTableLocator()->get('Notifications', $config);
    }

    public function testUnreadNotifications()
    {
        $userId = 200002;
        $query = $this->Notifications->fetchUnreadNotifications($userId);
        $this->assertInstanceOf('\Cake\ORM\Query', $query);
        $result = $query->disableHydration()->toArray();
        $expected = [
            [
                'id' => 1,
                'message' => 'Chefpipz liked your post',
                'user_id' => 200013,
                'user' => [
                    'username' => 'tobeFollowed',
                    'avatar_url' => null,
                ],
                'post_id' => 1,
                'link' => '/posts/1',
                'type' => 'liked',
            ],
        ];
        $this->assertEquals($result, $expected);
    }

    public function testReadNotifications()
    {
        $userId = 200002;
        $query = $this->Notifications->fetchReadNotifications($userId);
        $this->assertInstanceOf('\Cake\ORM\Query', $query);
        $result = $query->disableHydration()->toArray();
        $expected = [
            [
                'id' => 2,
                'message' => 'This is a read post',
                'user_id' => 200013,
                'user' => [
                    'username' => 'tobeFollowed',
                    'avatar_url' => null,
                ],
                'post_id' => 9,
                'link' => '/posts/1',
                'type' => 'liked',
            ],
        ];
        $this->assertEquals($result, $expected);
    }

    public function testCountUnreadNotifications()
    {
        $userId = 200002;
        $totalCount = $this->Notifications->countUnreadNotifications($userId);
        $expected = $this->Notifications->find()
            ->where([
                'receiver_id' => $userId,
                'is_read IS NULL'
            ])
            ->count();
        $this->assertEquals($totalCount, $expected);
    }

    public function testReadOne()
    {
        $userId = 200001;
        $notificationId = 3;
        $this->Notifications->read($notificationId);
        $totalCount = $this->Notifications->find()
            ->where([
                'id' => $notificationId,
                'is_read IS NULL'
            ])
            ->count();
        $this->assertEquals($totalCount, 0);
    }

    public function testReadAll()
    {
        $userId = 200002;
        $this->Notifications->readAll($userId);
        $totalCount = $this->Notifications->find()
            ->where([
                'receiver_id' => $userId,
                'is_read IS NULL'
            ])
            ->count();
        $this->assertEquals($totalCount, 0);
    }

    public function testAddingNotificationWithSameWillReturnFalse()
    {
        $postId = 9;
        $userId = 200002;
        $receiverId = $userId;
        $data = [
            'type' => 'commented',
            'receiver_id' => $receiverId,
            'post_id' => $postId,
            'user_id' => $userId,
        ];
        $result = $this->Notifications->addNotification($data);
        $this->assertEquals($result, false);
    }

    public function testAddingNotification()
    {
        $postId = 9;
        $userId = 200001;
        $receiverId = 26;
        $data = [
            'type' => 'commented',
            'receiver_id' => $receiverId,
            'post_id' => $postId,
            'user_id' => $userId,
        ];
        $result = $this->Notifications->addNotification($data);
        $this->assertEquals($result, true);
    }
}
