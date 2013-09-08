<?php

namespace AtomicPHP\FailureHandling\Tests;

use \Exception;
use \AtomicPHP\FailureHandling\Exceptions\ErrorException;
use \AtomicPHP\FailureHandling\Handlers\DefaultFailureHandler;

/**
 * DefaultFailureHandlerTest
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package AtomicPHP\FailureHandling\Tests
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
     * testHandleException
     *
     * Tests if DefaultFailureHandler::handleException logs the exception to the instance implementing LoggerInterface
     *
     * @access public
     * @return void
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
