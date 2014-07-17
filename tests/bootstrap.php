<?php
/**
 * @file
 * bootstrap.php
 */

// Register composer autoloader
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    throw new \RuntimeException(
        'Could not find vendor/autoload.php, make sure you ran composer.'
    );
}

require_once __DIR__.'/../vendor/autoload.php';
