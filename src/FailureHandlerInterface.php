<?php

namespace Nijens\FailureHandling;

use \Exception;

/**
 * FailureHandlerInterface
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package Nijens\Failurehandling
 **/
interface FailureHandlerInterface
{
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
