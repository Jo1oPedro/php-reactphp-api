<?php

namespace App\controllers;

use App\container\Container;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;

class StaticFilesController
{
    public function index(string $file): Response
    {
        $projectRoot = Container::getInstance()->get('BASE_PATH');
        $path = $projectRoot . "/uploads/" . $file;
        return new Response(
            200,
            ['Content-Type' => 'image/png'],
            file_get_contents($path)
        );
    }
}