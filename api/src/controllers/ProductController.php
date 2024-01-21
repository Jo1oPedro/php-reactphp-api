<?php

namespace App\controllers;

use React\Http\Message\Response;

class ProductController
{
    public function index()
    {
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