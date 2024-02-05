<?php

namespace App\middlewares\handlers;

use App\http\Response;
use Psr\Http\Message\ServerRequestInterface;

class ErrorHandler
{
    public function __invoke(ServerRequestInterface $serverRequest, callable $next)
    {
        try {
            return $next($serverRequest);
        } catch (\Throwable $throwable) {
            return Response::internalServerError($throwable->getMessage());
        }
    }
}