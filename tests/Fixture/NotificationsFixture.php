<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * NotificationsFixture
 */
class NotificationsFixture extends TestFixture
{

    public $import = ['table' => 'notifications'];

    /**
     * Init method
     *
     * @return void
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'message' => 'Chefpipz liked your post',
                'user_id' => 200013,
                'receiver_id' => 200002,
                'post_id' => 1,
                'link' => '/posts/1',
                'is_read' => null,
                'type' => 'liked',
                'created' => '2019-11-29 09:21:59',
                'modified' => '2019-11-29 09:21:59'
            ],
            [
                'id' => 2,
                'message' => 'This is a read post',
                'user_id' => 200013,
                'receiver_id' => 200002,
                'post_id' => 9,
                'link' => '/posts/1',
                'is_read' => '2019-11-29 09:21:59',
                'type' => 'liked',
                'created' => '2019-11-29 09:21:59',
                'modified' => '2019-11-29 09:21:59'
            ],
            [
                'id' => 3,
                'message' => 'Another post',
                'user_id' => 200013,
                'receiver_id' => 200001,
                'post_id' => 8,
                'link' => '/posts/1',
                'is_read' => null,
                'type' => 'liked',
                'created' => '2019-11-29 09:21:59',
                'modified' => '2019-11-29 09:21:59'
            ],
            [
                'id' => 4,
                'message' => 'Read Post',
                'user_id' => 321,
                'receiver_id' => 123,
                'post_id' => 8,
                'link' => '/posts/1',
                'is_read' => '2019-11-30 09:21:59',
                'type' => 'liked',
                'created' => '2019-11-29 09:21:59',
                'modified' => '2019-11-29 09:21:59'
            ],
        ];
        parent::init();
    }
}
