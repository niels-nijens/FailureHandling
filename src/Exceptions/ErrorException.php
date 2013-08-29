<?php

namespace AtomicPHP\FailureHandling\Exceptions;

/**
 * ErrorException
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package AtomicPHP\Failurehandling\Exceptions
 **/
class ErrorException extends \ErrorException {

    /**
     * getSeverityLevel
     *
     * Returns the severity level based on @see ErrorException::getCode
     *
     * @access public
     * @return string
     **/
    public function getSeverityLevel() {
        switch ($this->getCode() ) {
            case E_ERROR:
            case E_USER_ERROR:
                return SeverityLevel::ERROR;
                break;
            case E_WARNING:
            case E_USER_WARNING:
                return SeverityLevel::WARNING;
                break;
            case E_STRICT:
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return SeverityLevel::NOTICE;
                break;
            default:
                return SeverityLevel::CRITICAL;
                break;
        }
    }
}
