<?php

namespace AtomicPHP\FailureHandling\Handlers;

use \Exception;
use \AtomicPHP\FailureHandling\Exceptions\ErrorException;
use \AtomicPHP\FailureHandling\FailureHandlerInterface;
use \Psr\Log\LoggerAwareInterface;
use \Psr\Log\LoggerInterface;

/**
 * DefaultFailureHandler
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package AtomicPHP\Failurehandling\Handlers
 **/
class DefaultFailureHandler implements FailureHandlerInterface, LoggerAwareInterface
{
    /**
     * The logger instance
     *
     * @access protected
     * @var    LoggerInterface
     **/
    protected $logger;

    /**
     * setLogger
     *
     * Sets the logger instance to log the failures
     *
     * @access public
     * @param  LoggerInterface $logger
     * @return void
     **/
    public function setLogger(LoggerInterface $logger) {
        $this->logger = $logger;
    }

    /**
     * handleError
     *
     * Handles catchable errors to convert to Exceptions after conversion @see handleException is called
     *
     * @access public
     * @param  integer $errorNumber
     * @param  string  $message
     * @param  string  $file
     * @param  integer $line
     * @param  array   $context
     * @return boolean
     * @throws ErrorException
     * @todo   Implement $context
     **/
    public function handleError($errorNumber, $message, $file, $line, array $context = array() ) {
        // error_reporting is disabled for this type of error or an @ error-control operator
        // was prepended to an expression. (@see http://php.net/manual/en/language.operators.errorcontrol.php)
        if ( (error_reporting() & $errorNumber) == false) {
            return false;
        }

        throw new ErrorException($message, $errorNumber, 0, $file, $line);
    }

    /**
     * handleException
     *
     * Handles Exceptions
     *
     * @access public
     * @param  Exception $exception
     * @return void
     * @todo   Make default / fallback log level configurable
     **/
    public function handleException(Exception $exception) {
        if (method_exists($exception, "getSeverityLevel") ) {
            $this->logger->log($exception->getSeverityLevel(), $exception->getMessage(), $this->getExceptionContext($exception) );
        }
        else {
            $this->logger->critical($exception->getMessage(), $this->getExceptionContext($exception) );
        }
    }

    /**
     * getExceptionContext
     *
     * Returns an array with context information of $exception
     *
     * @access protected
     * @param  Exception $exception
     * @return array
     **/
    protected function getExceptionContext(Exception $exception) {
        return array(
            "exception" => $exception,
        );
    }
}
