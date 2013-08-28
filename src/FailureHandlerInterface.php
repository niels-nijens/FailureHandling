<?php

namespace AtomicPHP\Failurehandling;

use \Psr\Log\LoggerInterface;

/**
 * FailureHandlerInterface
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package AtomicPHP\Failurehandling
 **/
interface FailureHandlerInterface {

    /**
     * setLogger
     *
     * Sets the logger instance to log the failures
     *
     * @access public
     * @param  LoggerInterface $logger
     * @return void
     **/
    public function setLogger(LoggerInterface $logger);

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
     **/
    public function handleError($errorNumber, $message, $file, $line, array $context = array() );

    /**
     * handleException
     *
     * Handles Exceptions
     *
     * @access public
     * @param  Exception $e
     * @return void
     **/
    public function handleException(Exception $e);
}
