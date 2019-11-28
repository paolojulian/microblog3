<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PostsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PostsTable Test Case
 */
class PostsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PostsTable
     */
    public $Posts;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Posts',
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
        $config = TableRegistry::getTableLocator()->exists('Posts') ? [] : ['className' => PostsTable::class];
        $this->Posts = TableRegistry::getTableLocator()->get('Posts', $config);
    }

    public function testSearchingWithSQLCommandWillReturnEmpty()
    {
        $text = 'DELETE * FROM posts';
        $page = 1;
        $result = $this->Posts->searchPosts($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals($result['list'], []);
    }

    public function testSearchingWithSpecialCharsWillReturnEmpty()
    {
        $text = '(*@#&!(*#&@!\\';
        $page = 1;
        $result = $this->Posts->searchPosts($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals($result['list'], []);
    }

    public function testSearchValidPostWillReturnWithTitleAndBody()
    {
        $text = 'search';
        $page = 1;

        $result = $this->Posts->searchPosts($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals(count($result['list']), 3);
        $this->assertEquals($result['list'][0]['id'], 7);
        $this->assertEquals($result['list'][1]['id'], 6);
        $this->assertEquals($result['list'][2]['id'], 5);
    }

    public function testSearchValidPostWillReturnExpected()
    {
        $text = 'Title Search';
        $page = 1;
        $result = $this->Posts->searchPosts($text, $page);
        $this->assertEquals(isset($result['list']), true);
        $this->assertEquals($result['totalCount'], 1);
    }
}
