<?php

use App\handlers\ErrorHandler;
use App\http\Router;
use App\http\RouterCollector;
use React\Http\HttpServer;
use React\Socket\SocketServer;

define('BASE_PATH', dirname(__DIR__));

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/routes/web.php';
require_once __DIR__ . '/../services/services.php';

$server = new HttpServer(
    new ErrorHandler(),
    new Router(RouterCollector::getInstance())
);

$socket = new SocketServer('0.0.0.0:7000');
$server->listen($socket);

$server->on('error', function (Throwable $throwable) {
    echo 'Error: ' . $throwable->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL . PHP_EOL;