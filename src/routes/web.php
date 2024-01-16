<?php

use App\controllers\ProductController;
use FastRoute\RouteCollector;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use function FastRoute\simpleDispatcher;

return simpleDispatcher(function (RouteCollector $routeCollector) {
    $routeCollector->addRoute('GET', '/products', [ProductController::class, 'index']);
    $routeCollector->addRoute('POST', '/products', [ProductController::class, 'store']);
    /*$routeCollector->get('/products', function (ServerRequestInterface $request) {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => 'GET request to /products'])
        );
    });*/
    /*$routeCollector->post('/products', function (ServerRequestInterface $request) {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => 'POST request to /products'])
        );
    });*/
});