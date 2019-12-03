<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ChatsFixture
 */
class ChatsFixture extends TestFixture
{
    public $import = ['table' => 'chats'];
    // @codingStandardsIgnoreEnd
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
                'message' => 'Lorem ipsum dolor sit amet',
                'is_read' => null,
                'user_id' => 200002,
                'receiver_id' => 200001,
                'created' => '2019-12-03 17:24:19',
                'modified' => 1575365059,
                'deleted' => null
            ],
        ];
        parent::init();
    }
}
