<?php

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    define('APPLICATION_ENV', 'prod');
}

defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'dev'));

define('WEB_PATH', __DIR__);

if (APPLICATION_ENV == 'dev') {
    require 'app_dev.php';
} else {
    require 'app.php';
}
