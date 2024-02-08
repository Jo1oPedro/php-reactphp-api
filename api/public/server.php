<?php

use App\http\Router;
use App\http\RouterCollector;
use App\middlewares\handlers\ErrorHandler;
use App\middlewares\JsonRequestDecoder;
use React\Http\HttpServer;
use React\Socket\SocketServer;

define('BASE_PATH', dirname(__DIR__));

echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;
echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;
echo 'memory_limit: ' . ini_get('memory_limit') . PHP_EOL;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/routes/web.php';
require_once __DIR__ . '/../config/services.php';

$middlewares = [
    new React\Http\Middleware\StreamingRequestMiddleware(),
    new React\Http\Middleware\LimitConcurrentRequestsMiddleware(100),
    new React\Http\Middleware\RequestBodyBufferMiddleware(4 * 1024 * 1024), // 16 MiB
    new React\Http\Middleware\RequestBodyParserMiddleware(8 * 1024 * 1024, 3),
    new ErrorHandler(),
    new JsonRequestDecoder(),
    new Router(RouterCollector::getInstance()),
];

$server = new HttpServer(...$middlewares);

$socket = new SocketServer('0.0.0.0:7000');
$server->listen($socket);

$server->on('error', function (Throwable $throwable) {
    dd($throwable);
    echo 'Error: ' . $throwable->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL . PHP_EOL;