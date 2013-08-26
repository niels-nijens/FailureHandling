<?php

namespace AtomicPHP\Failurehandling;

/**
 * FailureCatcher
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package AtomicPHP\Failurehandling
 **/
class FailureCatcher {

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
	public static function start(FailureHandlerInterface $failureHandler, $additionalShutdownCallback = null) {
		ob_start();
		if (php_sapi_name() === "cli") {
			ob_implicit_flush(true);
		}

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
	 * stop
	 *
	 * Stops the failure catcher
	 *
	 * @access public
	 * @return void
	 **/
	public static function stop() {
		if (static::$failureHandler instanceof FailureHandlerInterface) {
			restore_error_handler();
			restore_exception_handler();
		}

		if (static::$shutdownCallback instanceof UnregisterableCallback) {
			static::$shutdownCallback->unregister();
		}

		if (ob_get_length() > 0) {
			ob_end_clean();
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
	public static function shutdown() {
		$error = error_get_last();
		if (is_array($error) ) {
			$stacktrace = null;
			if (ob_get_length() > 0) {
				$stacktrace = ob_get_clean();
			}
			$context = array("stacktrace" => $stacktrace);

			static::$failureHandler->handleError($error["type"], $error["message"], $error["file"], $error["line"], $context);
		}

		if (is_callable(static::$additionalShutdownCallback) ) {
			call_user_func(static::$additionalShutdownCallback);
		}

		if (ob_get_length() > 0) {
			ob_end_clean();
		}
	}
}
