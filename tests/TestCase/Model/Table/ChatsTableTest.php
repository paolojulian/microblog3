<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ChatsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ChatsTable Test Case
 */
class ChatsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ChatsTable
     */
    public $Chats;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Chats',
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
        $config = TableRegistry::getTableLocator()->exists('Chats') ? [] : ['className' => ChatsTable::class];
        $this->Chats = TableRegistry::getTableLocator()->get('Chats', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Chats);

        parent::tearDown();
    }

    public function addMessage()
    {
    }
}
