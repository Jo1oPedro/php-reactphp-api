<?php

namespace App\controllers;

use App\http\Response;
use App\rabbitmq\RabbitmqManager;
use Psr\Http\Message\ServerRequestInterface;

class OrderController
{
    public function __construct(x $x)
    {
        //dd($x);
    }

    public function index(x $x, ServerRequestInterface $serverRequest)
    {
        throw new \Exception('dale12312312');
        RabbitmqManager::publishMessage(
            'produto_registrado',
            'mensagem produto registrado2'
        );

        /*return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => 'GET request to /orders'])
        );*/
        return Response::ok(json_encode(['message' => 'GET request to /orders']));
    }

    public function store()
    {
    }

    public function show(int $id)
    {
    }

    public function update(int $id)
    {
    }

    public function delete(int $id)
    {
    }
}