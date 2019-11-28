<?php
namespace App\Test\TestCase\Model\Behavior;

use App\Model\Behavior\TrimmerBehavior;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Behavior\TrimmerBehavior Test Case
 */
class TrimmerBehaviorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Behavior\TrimmerBehavior
     */
    public $Trimmer;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Trimmer = new TrimmerBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Trimmer);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
