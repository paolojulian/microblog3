<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LikesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LikesTable Test Case
 */
class LikesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LikesTable
     */
    public $Likes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Likes',
        'app.Posts',
        'app.Users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Likes') ? [] : ['className' => LikesTable::class];
        $this->Likes = TableRegistry::getTableLocator()->get('Likes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Likes);

        parent::tearDown();
    }

    public function testFetchLikersWillReturnArray()
    {
        $postId = 1;
        $query = $this->Likes->fetchByPost($postId);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->disableHydration()->toArray();
        $expected = [
            [
                'user_id' => 200001,
                'user' => [
                    'username' => 'existingusername',
                    'avatar_url' => '/testUrl',
                    'first_name' => 'Julian',
                    'last_name' => 'Paolo Vincent',
                ]
            ],
            [
                'user_id' => 200002,
                'user' => [
                    'username' => 'activated',
                    'avatar_url' => null,
                    'first_name' => 'Julian',
                    'last_name' => 'Paolo Vincent',
                ]
            ],
        ];
        $this->assertEquals($result, $expected);
    }

    public function testFetchLikersOfNonExistingPostWillReturnEmptyArray()
    {
        $postId = 1231;
        $query = $this->Likes->fetchByPost($postId);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->disableHydration()->toArray();
        $expected = [];
        $this->assertEquals($result, $expected);
    }

    public function testEmptyPageWillReturnEmptyArray()
    {
        $postId = 1;
        $page = 3;
        $query = $this->Likes->fetchByPost($postId, $page);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->disableHydration()->toArray();
        $expected = [];
        $this->assertEquals($result, $expected);
    }

    public function testRetrieveSecondPageWillDisplayExpected()
    {
        $postId = 1;
        $page = 2;
        $perPage = 1;
        $query = $this->Likes->fetchByPost($postId, $page, $perPage);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->disableHydration()->toArray();
        $expected = [
            [
                'user_id' => 200002,
                'user' => [
                    'username' => 'activated',
                    'avatar_url' => null,
                    'first_name' => 'Julian',
                    'last_name' => 'Paolo Vincent',
                ]
            ],
        ];
        $this->assertEquals($result, $expected);
    }
}
