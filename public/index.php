<?php

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config as ServiceManagerConfig;
use Zend\Stdlib\ArrayUtils;

chdir(dirname(__DIR__));
ini_set('display_errors', true);

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

include 'vendor/autoload.php';

$config = array();
foreach (glob('config/{,*.}{global,local}.php', GLOB_BRACE) as $file) {
    $config = ArrayUtils::merge($config, include $file);
}
$sl = new ServiceManager(new ServiceManagerConfig($config['service_manager']));
$sl->setService('config', $config);

$server = $sl->get('server');

$server->listen();

