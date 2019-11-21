<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\UserHandlerComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\UserHandlerComponent Test Case
 */
class UserHandlerComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\UserHandlerComponent
     */
    public $UserHandler;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->UserHandler = new UserHandlerComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UserHandler);

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
