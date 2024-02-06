<?php

use App\http\Router;
use App\http\RouterCollector;
use App\middlewares\handlers\ErrorHandler;
use App\middlewares\JsonRequestDecoder;
use React\Http\HttpServer;
use React\Socket\SocketServer;

define('BASE_PATH', dirname(__DIR__));

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/routes/web.php';
require_once __DIR__ . '/../config/services.php';

$middlewares = [
    new ErrorHandler(),
    new JsonRequestDecoder(),
    new Router(RouterCollector::getInstance())
];

$server = new HttpServer(...$middlewares);

$socket = new SocketServer('0.0.0.0:7000');
$server->listen($socket);

$server->on('error', function (Throwable $throwable) {
    dd($throwable);
    echo 'Error: ' . $throwable->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL . PHP_EOL;