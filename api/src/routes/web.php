<?php

use App\controllers\OrderController;
use App\controllers\ProductController;
use App\controllers\SignupController;
use App\controllers\StaticFilesController;
use App\http\RouterCollector;

$routeCollector = RouterCollector::getInstance();
$routeCollector->addRoute('GET', '/products', [ProductController::class, 'index']);
$routeCollector->addRoute('POST', '/products', [ProductController::class, 'store']);
$routeCollector->addRoute('GET', '/products/{id:\d+}', [ProductController::class, 'show']);
$routeCollector->addRoute('PUT', '/products/{id:\d+}', [ProductController::class, 'update']);
$routeCollector->addRoute('DELETE', '/products/{id:\d+}', [ProductController::class, 'delete']);

$routeCollector->addRoute('GET', '/orders', [OrderController::class, 'index']);
$routeCollector->addRoute('POST', '/orders', [OrderController::class, 'store']);
$routeCollector->addRoute('GET', '/orders/{id:\d+}', [OrderController::class, 'show']);
$routeCollector->addRoute('PUT', '/orders/{id:\d+}', [OrderController::class, 'update']);
$routeCollector->addRoute('DELETE', '/orders/{id:\d+}', [OrderController::class, 'delete']);

$routeCollector->addRoute('GET', '/uploads/{file:.*\.\w+}', [StaticFilesController::class, 'index']);

$routeCollector->post('/auth/signup', [SignupController::class, 'signup']);

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
