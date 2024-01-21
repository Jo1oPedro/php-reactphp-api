<?php

use App\http\Router;
use App\http\RouterCollector;
use React\Http\HttpServer;
use React\Socket\SocketServer;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../src/routes/web.php";

$server = new HttpServer(
    new Router(
        RouterCollector::getInstance()
    )
);

$socket = new SocketServer('0.0.0.0:7000');
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL;