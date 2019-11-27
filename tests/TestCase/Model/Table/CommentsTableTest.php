<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CommentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CommentsTable Test Case
 */
class CommentsTableTest extends TestCase
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

    public function testAddCommentWithNoBodyWillHaveError()
    {
        $data = ['body' => ''];
        $entity = $this->Comments->newEntity($data);
        $this->assertEquals($entity->hasErrors(), true);
    }

    public function testAddingCommentWithNonExistingBodyWillReturnError()
    {
        $postId = 1;
        $userId = 200002;
        $data = [];
        $result = $this->Comments->addCommentToPost($postId, $userId, $data);
        $this->assertInstanceOf('\App\Model\Entity\Comment', $result);
        $this->assertEquals($result->hasErrors(), true);
        $this->assertNotEquals($result->errors(), []);
    }

    public function testAddingCommentWithoutBodyWillReturnError()
    {
        $postId = 1;
        $userId = 200002;
        $data = ['body' => ''];
        $result = $this->Comments->addCommentToPost($postId, $userId, $data);
        $this->assertInstanceOf('\App\Model\Entity\Comment', $result);
        $this->assertEquals($result->hasErrors(), true);
        $this->assertNotEquals($result->errors(), []);
    }

    public function testAddValidComment()
    {
        $data = ['body' => 'New Comment!'];

        $entity = $this->Comments->newEntity($data);
        $entity->post_id = 1;
        $entity->user_id = 200002;
        $this->assertEquals($entity->errors(), []);
        $result = $this->Comments->save($entity);
        $this->assertNotEquals($result, false);
        $this->assertEquals($entity->body, 'New Comment!');
    }

    public function testAddValidCommentWillReturnEntityWithNoErrors()
    {
        $data = ['body' => 'New Comment!'];
        $postId = 1;
        $userId = 200002;
        $result = $this->Comments->addCommentToPost($postId, $userId, $data);
        $this->assertInstanceOf('\App\Model\Entity\Comment', $result);
        $this->assertEquals($result->hasErrors(), false);
    }
}
