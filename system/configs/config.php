<?php

$config = [];

$config['app']['name'] = 'Web FTP Manager';
$config['app']['debug'] = false;
$config['app']['timezone'] = 'America/Los_Angeles';


$config['ftp']['name'] = 'localhost';
$config['ftp']['server'] = 'localhost';
$config['ftp']['port'] = 21;
$config['ftp']['passive'] = false;
$config['ftp']['ssl'] = false;
$config['ftp']['timezone'] = 'UTC';

if(file_exists(__DIR__ . '/config.local.php')){
    include __DIR__ . '/config.local.php';
}

return $config;
