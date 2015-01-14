<?php
/**
 * @file
 * bootstrap.php
 */

// Ensure that composer has installed all dependencies
if (!file_exists(dirname(__DIR__) . '/composer.lock')) {
    throw new \RuntimeException(
        "Dependencies must be installed using composer:\n\n"
        . "php composer.phar install\n\n"
        . "See http://getcomposer.org for help with installing composer\n"
    );
}

// Register composer autoloader
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    throw new \RuntimeException(
        'Could not find vendor/autoload.php, make sure you ran composer.'
    );
}

// Include the composer autoloader
/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require dirname(__DIR__) . '/vendor/autoload.php';
$loader->addPsr4('Dkd\\PhpCmis\\Test\\', __DIR__);

// set timezone
date_default_timezone_set('Europe/Berlin');       // Set the default timezone
