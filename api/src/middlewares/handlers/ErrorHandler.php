<?php

namespace App\middlewares\handlers;

use App\http\Response;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;

class ErrorHandler
{
    public function __invoke(ServerRequestInterface $serverRequest, callable $next)
    {
        try {
            return $next($serverRequest);
        } catch (NestedValidationException $exception) {
            return Response::badRequest($exception->getMessages()['allOf']);
        } catch (\Throwable $throwable) {
            return Response::internalServerError($throwable->getMessage());
        }
    }
}