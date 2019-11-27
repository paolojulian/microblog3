<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * CommentsFixture
 */
class CommentsFixture extends TestFixture
{
    public $import = ['table' => 'comments'];
    public $records = [
        [
            'id' => 1,
            'body' => 'This is a comment on post 1',
            'post_id' => 1,
            'user_id' => 200001,
            'created' => '2019-11-26 09:25:33',
            'modified' => '2019-11-26 09:25:33',
            'deleted' => '2019-11-26 09:25:33'
        ],
        [
            'id' => 2,
            'body' => 'This is a comment on post 2',
            'post_id' => 1,
            'user_id' => 200001,
            'created' => '2019-11-26 09:25:33',
            'modified' => '2019-11-26 09:25:33',
            'deleted' => '2019-11-26 09:25:33'
        ],
        [
            'id' => 3,
            'body' => 'This is a comment on post 2',
            'post_id' => 2,
            'user_id' => 200002,
            'created' => '2019-11-26 09:25:33',
            'modified' => '2019-11-26 09:25:33',
            'deleted' => '2019-11-26 09:25:33'
        ],
    ];
}
