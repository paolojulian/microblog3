<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChatTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChatTable Test Case
 */
class ChatTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ChatTable
     */
    public $Chat;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Chat',
        'app.Users',
        'app.Receivers'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Chat') ? [] : ['className' => ChatTable::class];
        $this->Chat = TableRegistry::getTableLocator()->get('Chat', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Chat);

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
