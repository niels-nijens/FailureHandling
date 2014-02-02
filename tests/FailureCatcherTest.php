<?php

namespace Nijens\FailureHandling\Tests;

use PHPUnit_Framework_TestCase;
use Nijens\FailureHandling\FailureCatcher;
use Nijens\FailureHandling\Handlers\DefaultFailureHandler;

/**
 * FailureCatcherTest
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package Nijens\FailureHandling\Tests
 **/
class FailureCatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * The boolean indicating if the additional shutdown callback has been called
     *
     * @access public
     * @var    boolean
     **/
    public static $additionalShutdownCallbackCalled = false;

    /**
     * setUp
     *
     * Resets the static::$additionalShutdownCallbackCalled boolean
     *
     * @access public
     * @return void
     **/
    public function setUp() {
        static::$additionalShutdownCallbackCalled = false;
    }

    /**
     * tearDown
     *
     * Resets the static::$additionalShutdownCallbackCalled boolean and stops the FailureCatcher
     *
     * @access public
     * @return void
     **/
    public function tearDown() {
        static::$additionalShutdownCallbackCalled = false;

        FailureCatcher::stop();
    }

    /**
     * testStart
     *
     * Tests if:
     * - An output buffer is started
     * - The error handler is set to DefaultFailureHandler::handleError
     * - The exception hander is set to DefaultFailureHandler::handleException
     *
     * @access public
     * @return void
     **/
    public function testStart() {
        $failureHandler = new DefaultFailureHandler();

        FailureCatcher::start($failureHandler);

        $this->assertCount(2, ob_get_status(true) );

        $this->assertEquals(array($failureHandler, "handleError"), set_error_handler(function(){} ) );
        $this->assertEquals(array($failureHandler, "handleException"), set_exception_handler(function(){} ) );

        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * testStop
     *
     * Tests if:
     * - The started output buffer is stopped
     * - The error handler is restored to the previous / default error handler
     * - The exception hander is restored to the previous / default error handler
     *
     * @depends testStart
     * @access  public
     * @return  void
     **/
    public function testStop() {
        $failureHandler = new DefaultFailureHandler();

        FailureCatcher::start($failureHandler);

        $this->assertCount(2, ob_get_status(true) );

        FailureCatcher::stop();

        $this->assertCount(1, ob_get_status(true) );

        $this->assertNotEquals(array($failureHandler, "handleError"), set_error_handler(function(){} ) );
        $this->assertNotEquals(array($failureHandler, "handleException"), set_exception_handler(function(){} ) );

        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * testStopWithFlushOutputBufferWithoutBufferLength
     *
     * Tests if:
     * - The started output buffer is stopped
     * - The following error is not thrown:
     *    "ob_end_clean(): failed to delete buffer. No buffer to delete"
     *
     * @depends testStop
     * @access  public
     * @return  void
     **/
    public function testStopWithFlushOutputBufferWithoutBufferLength() {
        ob_start();

        $this->assertCount(2, ob_get_status(true) );

        FailureCatcher::stop(true);

        $this->assertCount(1, ob_get_status(true) );
    }

    /**
     * testStopWithFlushOutputBufferWithBufferLength
     *
     * Tests if the output buffer is stopped and flushed in FailureCatcher::stop with argument true
     *
     * @depends testStop
     * @access  public
     * @return  void
     **/
    public function testStopWithFlushOutputBufferWithBufferLength() {
        ob_start();
        ob_start();

        $this->fillOutputbuffer();

        $this->assertCount(3, ob_get_status(true) );

        FailureCatcher::stop(true);

        $this->assertEquals(2, strlen(ob_get_clean() ) );
        $this->assertCount(1, ob_get_status(true) );
    }

    /**
     * testStartWithAdditionalShutdownCallbackAndIsCalledInShutdown
     *
     * Tests if the set FailureCatcherTest::additionalShutdownCallback callable is called in FailureCatcher::shutdown
     *
     * @depends testStart
     * @depends testStop
     * @access  public
     * @return  void
     **/
    public function testStartWithAdditionalShutdownCallbackAndIsCalledInShutdown() {
        $failureHandler = new DefaultFailureHandler();

        FailureCatcher::start($failureHandler, array($this, "additionalShutdownCallback") );

        FailureCatcher::shutdown();

        $this->assertTrue(static::$additionalShutdownCallbackCalled);

        FailureCatcher::stop();
    }

    /**
     * testShutdownOutputbufferStopped
     *
     * Tests if the output buffer is stopped and cleaned in FailureCatcher::shutdown
     *
     * @depends testStart
     * @depends testStop
     * @access  public
     * @return  void
     **/
    public function testShutdownOutputbufferStopped() {
        ob_start();
        ob_start();

        $this->fillOutputbuffer();

        $this->assertEquals(2, ob_get_length() );

        FailureCatcher::shutdown();

        $this->assertEquals(0, ob_get_length() );
    }

    /**
     * additionalShutdownCallback
     *
     * This method is used as additional shutdown callback to run test @see testStartWithAdditionalShutdownCallbackAndIsCalledInShutdown
     *
     * @access public
     * @return void
     **/
    public function additionalShutdownCallback() {
        static::$additionalShutdownCallbackCalled = true;
    }

    /**
     * fillOutputbuffer
     *
     * Fills the output buffer without being visible in CLI
     *
     * @access protected
     * @return void
     **/
    protected function fillOutputbuffer() {
        print " \010";
    }
}
