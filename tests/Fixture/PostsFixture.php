<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PostsFixture
 */
class PostsFixture extends TestFixture
{
    public $import = ['table' => 'posts'];

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
                'title' => '20002 Post',
                'body' => 'Lorem Ipsum',
                'retweet_post_id' => null,
                'user_id' => 200002,
                'img_path' => null,
                'created' => '2019-11-22 09:35:10',
                'modified' => '2019-11-22 09:35:10',
                'deleted' => null
            ],
            [
                'id' => 2,
                'title' => '20002 Post',
                'body' => 'Lorem Ipsum',
                'retweet_post_id' => null,
                'user_id' => 200001,
                'img_path' => null,
                'created' => '2019-11-22 09:35:10',
                'modified' => '2019-11-22 09:35:10',
                'deleted' => null
            ],
            [
                'id' => 3,
                'title' => '20002 Post',
                'body' => 'Lorem Ipsum',
                'retweet_post_id' => null,
                'user_id' => 200012,
                'img_path' => null,
                'created' => '2019-11-22 09:35:10',
                'modified' => '2019-11-22 09:35:10',
                'deleted' => null
            ],
            [
                'id' => 4,
                'title' => '',
                'body' => 'This is a shared post',
                'retweet_post_id' => 1,
                'user_id' => 200001,
                'img_path' => null,
                'created' => '2019-11-22 09:35:10',
                'modified' => '2019-11-22 09:35:10',
                'deleted' => null
            ],
        ];
        parent::init();
    }
}
