<?php

namespace App\controllers;

use App\rabbitmq\RabbitmqManager;
use React\Http\Message\Response;

class ProductController
{
    public function index()
    {
        RabbitmqManager::publishMessage(
            'produto_registrado',
            'mensagem produto registrado2'
        );

        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => 'GET request to /products'])
        );
    }

    public function store()
    {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => 'POST request to /products'])
        );
    }
}