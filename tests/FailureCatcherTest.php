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

        FailureCatcher::stop();

        $this->assertNotEquals(array($failureHandler, "handleError"), set_error_handler(function(){} ) );
        $this->assertNotEquals(array($failureHandler, "handleException"), set_exception_handler(function(){} ) );

        restore_error_handler();
        restore_exception_handler();
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

    public function testSetPHPConfigurationOptionsToAvoidErrorOutputAreWrong() {
        $previousLogErrors = ini_set("log_errors", "1");
        $previousErrorLog = ini_set("error_log", "");
        $previousDisplayErrors = ini_set("display_errors", "0");

        FailureCatcher::start(new DefaultFailureHandler() );
        FailureCatcher::stop();

        $this->assertEquals("0", ini_get("log_errors") );
        $this->assertEquals("0", ini_get("display_errors") );

        ini_set("log_errors", $previousLogErrors);
        ini_set("error_log", $previousErrorLog);
        ini_set("display_errors", $previousDisplayErrors);
    }

    public function testSetPHPConfigurationOptionsToAvoidErrorOutputAreRight() {
        $previousLogErrors = ini_set("log_errors", "0");
        $previousErrorLog = ini_set("error_log", "/tmp/error_log");
        $previousDisplayErrors = ini_set("display_errors", "1");

        FailureCatcher::start(new DefaultFailureHandler() );
        FailureCatcher::stop();

        $this->assertEquals("0", ini_get("log_errors") );
        $this->assertEquals("0", ini_get("display_errors") );

        ini_set("log_errors", $previousLogErrors);
        ini_set("error_log", $previousErrorLog);
        ini_set("display_errors", $previousDisplayErrors);
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
}
