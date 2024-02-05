<?php

namespace App\controllers;

use App\rabbitmq\RabbitmqManager;
use Psr\Http\Message\ServerRequestInterface;
//use React\Http\Message\Response;
use App\http\Response;
class ProductController extends Controller
{
    public function __construct(x $x)
    {
        //dd($x);
    }

    public function index(x $x, ServerRequestInterface $serverRequest)
    {
        RabbitmqManager::publishMessage(
            'produto_registrado',
            'mensagem produto registrado2'
        );

        return (new Response())(
            200,
            json_encode(['message' => 'GET request to /products'])
        );
    }

    public function store(ServerRequestInterface $request)
    {
        $product = [
            'name' => $request->getParsedBody()['name'],
            'price' => $request->getParsedBody()['price']
        ];

        return Response::ok(
            json_encode([
                'message' => 'POST request to /products',
                'product' => $product
            ]),
        );
    }

    public function show(int $id)
    {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => "GET request to /products/{$id}"])
        );
    }

    public function update(int $id)
    {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => "PUT request to /products/{$id}"])
        );
    }

    public function delete(int $id)
    {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => "DELETE request to /products/{$id}"])
        );
    }
}