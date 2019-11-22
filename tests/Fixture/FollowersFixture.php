<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;
use Cake\Auth\DefaultPasswordHasher;

/**
 * FollowersFixture
 */
class FollowersFixture extends TestFixture
{
    public $import = ['table' => 'followers'];
    public $records = [
        [
            'id' => 1,
            'user_id' => 200001,
            'following_id' => 200002,
            'created' => '2019-11-20 03:47:52',
            'modified' => '2019-11-20 03:47:52',
            'deleted' => null
        ],
        [
            'id' => 2,
            'user_id' => 200002,
            'following_id' => 200001,
            'created' => '2019-11-20 03:47:52',
            'modified' => '2019-11-20 03:47:52',
            'deleted' => null
        ],
        [
            'id' => 3,
            'user_id' => 200001,
            'following_id' => 200012,
            'created' => '2019-11-20 03:47:52',
            'modified' => '2019-11-20 03:47:52',
            'deleted' => null
        ],
    ];

}
