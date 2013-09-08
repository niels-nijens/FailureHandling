<?php

namespace AtomicPHP\FailureHandling\Tests;

use \AtomicPHP\FailureHandling\SeverityLevel;
use \AtomicPHP\FailureHandling\Exceptions\ErrorException;

/**
 * ErrorExceptionTest
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package AtomicPHP\FailureHandling\Tests
 **/
class ErrorExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testGetSeverityLevel
     *
     * Tests if ErrorException::getSeverityLevel returns $severityLevel for $errorConstant
     *
     * @dataProvider getErrorConstantsWithSeverityLevel
     * @access public
     * @param integer $errorConstant
     * @param string $severityLevel
     * @return void
     **/
    public function testGetSeverityLevel($errorConstant, $severityLevel) {
        $exception = new ErrorException("Test exception", $errorConstant, 0, __FILE__, __LINE__);

        $this->assertEquals($severityLevel, $exception->getSeverityLevel() );
    }

    /**
     * getErrorConstantsWithSeverityLevel
     *
     * Returns an array with error constants and accompanying severity levels
     *
     * @access public
     * @return array
     **/
    public function getErrorConstantsWithSeverityLevel() {
        return array(
            array(E_ERROR, SeverityLevel::ERROR),
            array(E_WARNING, SeverityLevel::WARNING),
            array(E_STRICT, SeverityLevel::NOTICE),
            array(E_NOTICE, SeverityLevel::NOTICE),
            array(E_USER_ERROR, SeverityLevel::ERROR),
            array(E_USER_WARNING, SeverityLevel::WARNING),
            array(E_USER_NOTICE, SeverityLevel::NOTICE),
            array(E_RECOVERABLE_ERROR, SeverityLevel::ERROR),
            array(E_DEPRECATED, SeverityLevel::NOTICE),
            array(E_USER_DEPRECATED, SeverityLevel::NOTICE),
            array(0, SeverityLevel::CRITICAL),
        );
    }
}
