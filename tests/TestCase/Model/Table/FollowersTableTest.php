<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FollowersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FollowersTable Test Case
 */
class FollowersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FollowersTable
     */
    public $Followers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Followers',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Followers') ? [] : ['className' => FollowersTable::class];
        $this->Followers = TableRegistry::getTableLocator()->get('Followers', $config);
        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Followers);

        parent::tearDown();
    }

    public function testFetchFollowersWillReturnArray()
    {
        $followers = $this->Followers->fetchFollowers(200002);
        $this->assertEquals($followers[0]->user->id, 200001);
        $this->assertEquals(is_array($followers), true);
    }

    public function testFetchNoFollowersWillReturnEmptyArray()
    {
        $followers = $this->Followers->fetchFollowers(123);
        $this->assertEquals($followers, []);
        $this->assertEquals(is_array($followers), true);
    }

    public function testFetchFollowingWillReturnArray()
    {
        $followers = $this->Followers->fetchFollowing(200002);
        $this->assertEquals(is_array($followers), true);
    }

    public function testFetchNoFollowingWillReturnEmptyArray()
    {
        $followers = $this->Followers->fetchFollowing(123);
        $this->assertEquals($followers, []);
        $this->assertEquals(is_array($followers), true);
    }
}
