<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CommentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CommentsTable Test Case
 */
class FetchCommentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CommentsTable
     */
    public $Comments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Comments',
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
        $config = TableRegistry::getTableLocator()->exists('Comments') ? [] : ['className' => CommentsTable::class];
        $this->Comments = TableRegistry::getTableLocator()->get('Comments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Comments);

        parent::tearDown();
    }

    public function testFetchingCommentsWillReturnExactAmount()
    {
        $postId = 1;
        $page = 1;
        $query = $this->Comments->fetchPerPost($postId, $page);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->enableHydration(false)->toArray();
        $this->assertEquals(is_array($result), true);
    }

    public function testFetchingCommentsOfNonExistingPostWillReturnEmptyArray()
    {
        $postId = 103219312;
        $page = 1;
        $query = $this->Comments->fetchPerPost($postId, $page);
        $this->assertInstanceOf('Cake\ORM\Query', $query);
        $result = $query->enableHydration(false)->toArray();
        $this->assertEquals(is_array($result), true);
        $this->assertEquals($result, []);
    }
}
