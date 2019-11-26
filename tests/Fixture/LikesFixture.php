<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * LikesFixture
 */
class LikesFixture extends TestFixture
{
    public $import = ['table' => 'likes'];
    public $records = [
        [
            'id' => 1,
            'post_id' => 1,
            'user_id' => 200001,
            'created' => '2019-11-25 13:19:12',
            'modified' => '2019-11-25 13:19:12',
            'deleted' => '2019-11-25 13:19:12'
        ],
        [
            'id' => 2,
            'post_id' => 1,
            'user_id' => 200002,
            'created' => '2019-11-25 13:19:12',
            'modified' => '2019-11-25 13:19:12',
            'deleted' => '2019-11-25 13:19:12'
        ],
    ];
}
