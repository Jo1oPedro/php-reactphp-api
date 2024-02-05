<?php

namespace App\http;
use React\Http\Message\Response as ReactPhpResponse;

class Response
{
    public function __invoke(int $statusCode, mixed $data = null): ReactPhpResponse
    {
        $data = $data ? json_encode($data) : null;

        return new ReactPhpResponse(
            $statusCode,
            ['Content-type', 'application/json'],
            $data
        );
    }

    public static function ok($data): ReactPhpResponse
    {
        return new ReactPhpResponse(
          200,
          ['Content-type', 'application/json'],
          $data
        );
    }

    public static function error(int $code, string $reason): ReactPhpResponse
    {
        return new ReactPhpResponse(
            $code,
            ['Content-type' => 'application/json'],
            json_encode(['message' => $reason])
        );
    }

    public static function internalServerError(string $reason): ReactPhpResponse
    {
        return new ReactPhpResponse(
            500,
            ['Content-type' => 'application/json'],
            json_encode(['message' => $reason])
        );
    }

    public static function notFound(): ReactPhpResponse
    {
        return new ReactPhpResponse(404);
    }
}