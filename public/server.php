<?php

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;

require_once __DIR__ . '/../vendor/autoload.php';

/** @var Dispatcher $dispatcher */
$dispatcher = require_once __DIR__ . "/../src/routes/web.php";

//$routes = new RouteCollector(new Std(), new GroupCountBased());

$server = new HttpServer(function (ServerRequestInterface $request) use ($dispatcher) {
    $routeInfo = $dispatcher->dispatch(
        $request->getMethod(), $request->getUri()->getPath()
    );

    try {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new Response(404, ['Content-type' => 'text/plain'], json_encode('Not found'));
            case Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(405, ['Content-type' => 'text/plain'], json_encode('Method not allowed'));
            case Dispatcher::FOUND:
                $reflectionController = new ReflectionClass($routeInfo[1][0]);
                $method = $reflectionController->getMethod($routeInfo[1][1]);
                return $method->invoke($reflectionController->newInstance(), $routeInfo[2]);

            throw new LogicException('Something went wrong with routing');
        };
    } catch (Exception $exception) {
        dd($exception);
    }
});

$socket = new SocketServer('127.0.0.1:8000');
$server->listen($socket);

echo 'Listening on ' . str_replace('tcp', 'http', $socket->getAddress()) . PHP_EOL;