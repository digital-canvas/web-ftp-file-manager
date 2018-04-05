<?php

/**
 * |--------------------------------------------------------------------------------------
 * | Rather than modifying this file, create a config.local.php and put overrides there
 * |---------------------------------------------------------------------------------------
 */

$config = [];

// Displayed in the site header
$config['app']['name'] = 'Web FTP Manager';
// Set to true to display errors
$config['app']['debug'] = false;
// This is the timezone dates will be displayed in older browsers
// If supported it will use the user's local timezone instead
$config['app']['timezone'] = 'UTC';

// This is how the server is displayed in the ui
$config['ftp']['name'] = 'localhost';
// This is the ftp server it actually connects to
$config['ftp']['server'] = 'localhost';
$config['ftp']['port'] = 21;
// Might need to be disabled on some servers
$config['ftp']['passive'] = true;
// This is for ftps, not sftp
$config['ftp']['ssl'] = false;
// Set to timezone of server (it will assume file modification times are in this timezone)
$config['ftp']['timezone'] = 'UTC';

// Create a config.local.php rather than modify this file
if(file_exists(__DIR__ . '/config.local.php')){
    include __DIR__ . '/config.local.php';
}

return $config;
