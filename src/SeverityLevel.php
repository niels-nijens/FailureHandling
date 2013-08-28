<?php

namespace AtomicPHP\Failurehandling;

/**
 * SeverityLevel
 *
 * Implements RFC 5424 severity levels as constants @see http://tools.ietf.org/html/rfc5424
 *
 * @author  Niels Nijens <nijens.niels@gmail.com>
 * @package AtomicPHP\Failurehandling
 **/
class SeverityLevel {

    /**
     * Emergency: system is unusable
     **/
    const EMERGENCY = "emergency";

    /**
     * Alert: action must be taken immediately
     **/
    const ALERT = "alert";

    /**
     * Critical: critical conditions
     **/
    const CRITICAL = "critical";

    /**
     * Error: error conditions
     **/
    const ERROR = "error";

    /**
     * Warning: warning conditions
     **/
    const WARNING = "warning";

    /**
     * Notice: normal but significant condition
     **/
    const NOTICE = "notice";

    /**
     * Informational: informational messages
     **/
    const INFO = "info";

    /**
     * Debug: debug-level messages
     **/
    const DEBUG = "debug";
}
