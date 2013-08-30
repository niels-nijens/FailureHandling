<?php
/**
 * Bootstrap file for PHPUnit tests
 *
 * @author Niels Nijens <nijens.niels@gmail.com>
 */
require_once __DIR__ . "/../vendor/autoload.php";

spl_autoload_register(function($className) {
    $vendorNamespace = "AtomicPHP\\FailureHandling\\";
    if (strpos($className, $vendorNamespace) === 0) {
        $classNameFile = substr($className, strlen($vendorNamespace) ) . ".php";
        include __DIR__ . "/../src/" . str_replace("\\", DIRECTORY_SEPARATOR, $classNameFile);
    }
});
