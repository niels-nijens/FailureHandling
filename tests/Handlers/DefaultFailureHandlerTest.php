<?php

namespace Nijens\FailureHandling\Tests;

use Exception;
use Nijens\FailureHandling\Exceptions\ErrorException;
use Nijens\FailureHandling\Handlers\DefaultFailureHandler;

/**
 * DefaultFailureHandlerTest
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package Nijens\FailureHandling\Tests
 **/
class DefaultFailureHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testHandleError
     *
     * Tests if DefaultFailureHandler::handleError throws an ErrorException
     *
     * @expectedException ErrorException
     * @expectedExceptionCode E_ERROR
     * @expectedExceptionMessage Error message
     * @access public
     * @return void
     **/
    public function testHandleError() {
        $failureHandler = new DefaultFailureHandler();
        $failureHandler->handleError(E_ERROR, "Error message", __FILE__, __LINE__);
    }

    /**
     * testHandleErrorReturnsFalse
     *
     * Tests if DefaultFailureHandler::handleError returns false
     *
     * @access public
     * @return void
     * @todo Assert logger contents
     **/
    public function testHandleErrorReturnsFalse() {
        $previousLevel = error_reporting(0);

        $failureHandler = new DefaultFailureHandler();
        $this->assertFalse($failureHandler->handleError(E_NOTICE, "Error message", __FILE__, __LINE__) );

        error_reporting($previousLevel);
    }

    /**
     * testHandleException
     *
     * Tests if DefaultFailureHandler::handleException logs the exception to the instance implementing LoggerInterface
     *
     * @access public
     * @return void
     * @todo Assert logger contents
     **/
    public function testHandleException() {
        $failureHandler = new DefaultFailureHandler();

        $mockLogger = $this->getMock("\Psr\Log\LoggerInterface");

        $failureHandler->setLogger($mockLogger);
        $failureHandler->handleException(new Exception("Exception message") );
    }

    /**
     * testHandleExceptionAfterHandleError
     *
     * Tests if the Exception thrown in DefaultFailureHandler::handleError is logged correctly to the instance implementing LoggerInterface
     *
     * @access public
     * @return void
     * @todo Assert logger contents
     **/
    public function testHandleExceptionAfterHandleError() {
        $mockLogger = $this->getMock("\Psr\Log\LoggerInterface");

        $failureHandler = new DefaultFailureHandler();
        $failureHandler->setLogger($mockLogger);

        try {
            $failureHandler->handleError(E_ERROR, "Error message", __FILE__, __LINE__);
        }
        catch (ErrorException $exception) {
            $failureHandler->handleException($exception);
        }
    }
}
