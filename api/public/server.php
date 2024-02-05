<?php

use App\container\Container;
use App\http\Router;
use App\http\RouterCollector;
use App\middlewares\handlers\ErrorHandler;
use App\middlewares\JsonRequestDecoder;
use React\Http\HttpServer;
use React\MySQL\Factory;
use React\Socket\Connector;
use React\Socket\SocketServer;

define('BASE_PATH', dirname(__DIR__));

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/routes/web.php';
require_once __DIR__ . '/../services/services.php';

$middlewares = [
    new ErrorHandler(),
    new JsonRequestDecoder(),
    new Router(RouterCollector::getInstance())
];

$container = Container::getInstance();

$serviceIp = gethostbyname('banco_de_dados_relacional');

// SÓ É POSSÍVEL CONECTAR AO MYSQL LATEST OU 8+ UTILIZANDO ESSE CONNECTOR DEVIDO A ALGUNS PROBLEMAS DA BIBLIOTECA
$connector = new Connector([
    'dns' => $serviceIp,
    'tcp' => [
        // We have to set this correct, otherwise you get that error:
        // https://github.com/friends-of-reactphp/mysql/issues/112
        'bindto' => "{$serviceIp}:3306",
    ],
    'tls' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ]
]);

$mysql = new Factory(connector: $connector);
$connection = $mysql->createLazyConnection(
"{$container->get('MYSQL_USER')}:{$container->get('MYSQL_PASSWORD')}@{$serviceIp}:3306/{$container->get('MYSQL_DATABASE')}"
);

$server = new HttpServer(...$middlewares);

$socket = new SocketServer('0.0.0.0:7000');
$server->listen($socket);

$server->on('error', function (Throwable $throwable) {
    echo 'Error: ' . $throwable->getMessage() . PHP_EOL;
});

echo 'Listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL . PHP_EOL;