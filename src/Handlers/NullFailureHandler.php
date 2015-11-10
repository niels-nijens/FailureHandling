<?php

namespace Nijens\FailureHandling\Handlers;

use Exception;
use Nijens\FailureHandling\FailureHandlerInterface;

/**
 * NullFailureHandler
 *
 * Tries to handle nothing
 *
 * @author  Giso Stallenberg <giso@connectholland.nl>
 * @package Nijens\Failurehandling\Handlers
 **/
class NullFailureHandler implements FailureHandlerInterface
{

    /**
     * handleError
     *
     * Tells php to do nothing
     *
     * @access public
     * @param  integer $errorNumber
     * @param  string  $message
     * @param  string  $file
     * @param  integer $line
     * @param  array   $context
     * @return boolean
     **/
    public function handleError($errorNumber, $message, $file, $line, array $context = array() )
    {
        return false;
    }

    /**
     * handleException
     *
     * Handles Exceptions
     *
     * @access public
     * @param  Exception $exception
     * @return void
     **/
    public function handleException(Exception $exception)
    {
    }
}
