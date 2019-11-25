<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\UploadImgHandlerComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\UploadImgHandlerComponent Test Case
 */
class UploadImgHandlerComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\UploadImgHandlerComponent
     */
    public $UploadImgHandler;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->UploadImgHandler = new UploadImgHandlerComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UploadImgHandler);

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
