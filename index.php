<?php
if ( ! defined( 'ROOT_DIR' ) ) {
    define( 'ROOT_DIR', __DIR__ );
}
if ( ! defined( 'SYSTEM_DIR' ) ) {
    define( 'SYSTEM_DIR', ROOT_DIR . DIRECTORY_SEPARATOR . 'system' );
}
if ( ! defined( 'APP_DIR' ) ) {
    define( 'APP_DIR', SYSTEM_DIR . DIRECTORY_SEPARATOR . 'app' );
}
if ( ! defined( 'STORAGE_DIR' ) ) {
    define( 'STORAGE_DIR', SYSTEM_DIR . DIRECTORY_SEPARATOR . 'storage' );
}
if ( ! defined( 'PUBLIC_DIR' ) ) {
    define( 'PUBLIC_DIR', ROOT_DIR );
}

error_reporting(-1);
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);


// Include autoloader
require __DIR__ . '/system/vendor/autoload.php';

/** @var \League\Container\Container $container */
$container = require __DIR__ . '/system/configs/services.php';

/** @var \Framework\Dispatcher\RouteDispatcher $dispatcher */
$dispatcher = $container->get('Framework\Dispatcher\RouteDispatcher');
$response = $dispatcher->dispatch();

// Send the response back to the browser
$response->send();
