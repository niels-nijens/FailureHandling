<?php

namespace AtomicPHP\FailureHandling\Tests;

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
}
