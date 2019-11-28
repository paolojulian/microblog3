<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
        $config = TableRegistry::getTableLocator()->exists('Users') ? [] : ['className' => UsersTable::class];
        $this->Users = TableRegistry::getTableLocator()->get('Users', $config);
    }

    public function testSearchingWithSQLCommandWillReturnEmpty()
    {
        $text = 'DELETE * FROM users';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals($result['list'], []);
    }

    public function testSearchingWithSpecialCharsWillReturnEmpty()
    {
        $text = '(*@#&!(*#&@!\\';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals($result['list'], []);
    }

    public function testSearchValidNameWillNotIncludeUnactivatedUsers()
    {
        $text = 'activated';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $expected = [
            [
                'id' => 200002,
                'username' => 'activated',
                'first_name' => 'Julian',
                'last_name' => 'Paolo Vincent',
                'avatar_url' => null,
            ],
            [
                'id' => 200012,
                'username' => 'anotherActivated',
                'first_name' => 'Julian',
                'last_name' => 'Paolo Vincent',
                'avatar_url' => null,
            ],
        ];
        $this->assertEquals($result['list'], $expected);
    }

    public function testSearchValidNameWillReturnTotalCount()
    {
        $text = 'activated';
        $page = 1;
        $result = $this->Users->searchUser($text, $page);
        $this->assertEquals(isset($result['totalCount']), true);
        $this->assertEquals($result['totalCount'], 2);
    }
}
