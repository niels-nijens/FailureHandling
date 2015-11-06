<?php

namespace Nijens\FailureHandling;

use Nijens\Utilities\UnregisterableCallback;

/**
 * FailureCatcher
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package Nijens\Failurehandling
 **/
class FailureCatcher
{
    /**
     * The failure handler instance implementing FailureHandlerInterface
     *
     * @access protected
     * @var    FailureHandlerInterface
     **/
    protected static $failureHandler;

    /**
     * The UnregisterableCallback instance
     *
     * @access protected
     * @var    UnregisterableCallback
     **/
    protected static $shutdownCallback;

    /**
     * The callable with an additional shutdown callback handled in @see shutdown
     *
     * @access protected
     * @var    callable
     **/
    protected static $additionalShutdownCallback;

    /**
     * start
     *
     * Starts the failure catcher with a $failureHandler
     *
     * @access public
     * @param  FailureHandlerInterface $failureHandler
     * @param  callable                $additionalShutdownCallback
     * @return void
     **/
    public static function start(FailureHandlerInterface $failureHandler, $additionalShutdownCallback = null)
    {
        static::setPHPConfigurationOptionsToAvoidErrorOutput();

        static::$failureHandler = $failureHandler;
        set_error_handler(array($failureHandler, "handleError") );
        set_exception_handler(array($failureHandler, "handleException") );

        static::$shutdownCallback = new UnregisterableCallback(array(__CLASS__, "shutdown") );
        register_shutdown_function(array(static::$shutdownCallback, "call") );

        if (is_callable($additionalShutdownCallback) ) {
            static::$additionalShutdownCallback = $additionalShutdownCallback;
        }
    }

    /**
     * setPHPConfigurationOptionsToAvoidErrorOutput
     *
     * Sets the ini options in a way that make sure errors are not outputted to the browser or cli
     *
     * @return boolean
     */
    private static function setPHPConfigurationOptionsToAvoidErrorOutput() {
        $changed = false;

        // an empty log while logging will produce output on errors that cannot be handled by an error-handler
        if (ini_get("log_errors") === "1" && ini_get("error_log") === "") {
            ini_set("log_errors", "0");
            $changed = true;
        }
        // do not output errors
        if (ini_get("display_errors") === "1") {
            ini_set("display_errors", "0");
            $changed = true;
        }

        return $changed;
    }

    /**
     * stop
     *
     * Stops the failure catcher
     *
     * @access public
     * @return void
     **/
    public static function stop()
    {
        if (static::$failureHandler instanceof FailureHandlerInterface) {
            restore_error_handler();
            restore_exception_handler();
        }

        if (static::$shutdownCallback instanceof UnregisterableCallback) {
            static::$shutdownCallback->unregister();
        }

        static::$failureHandler = null;
        static::$shutdownCallback = null;
        static::$additionalShutdownCallback = null;
    }

    /**
     * shutdown
     *
     * Handles errors that were not handled by FailureHandlerInterface::handleError
     *
     * @access public
     * @return void
     **/
    public static function shutdown()
    {
        $error = error_get_last();
        if (is_array($error) ) {
            static::handleShutdownError($error);
        }

        if (is_callable(static::$additionalShutdownCallback) ) {
            call_user_func(static::$additionalShutdownCallback);
        }
    }

    /**
     * handleShutdownError
     *
     * Handles errors that were not handled by FailureHandlerInterface::handleError
     *
     * @access protected
     * @param  array $error
     * @return void
     **/
    protected static function handleShutdownError(array $error)
    {
        $stacktrace = null;
        if (ob_get_length() > 0) {
            $stacktrace = ob_get_clean();
        }
        $context = array("stacktrace" => $stacktrace);

        static::$failureHandler->handleError($error["type"], $error["message"], $error["file"], $error["line"], $context);
    }
}
