<?php

use Zend\Stratigility\MiddlewarePipe;
use Zend\Diactoros\Server;
use MediaMonitor\Middleware\Home as HomeMiddleware;

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

$app = new MiddlewarePipe();
$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

$app->pipe('/', new HomeMiddleware);

$app->pipe('/api', function($req, $res, $next) {
    return $res->end(print_r($next, false));
});

$app->pipe('/', function($error, $req, $res, $next) {
    return $res->end('Not Found');
});

$server->listen();


