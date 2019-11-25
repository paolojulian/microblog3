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
        'app.Followings'
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

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
