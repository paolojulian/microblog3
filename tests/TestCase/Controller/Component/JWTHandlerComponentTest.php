<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\JWTHandlerComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\JWTHandlerComponent Test Case
 */
class JWTHandlerComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\JWTHandlerComponent
     */
    public $JWTHandler;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->JWTHandler = new JWTHandlerComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->JWTHandler);

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
