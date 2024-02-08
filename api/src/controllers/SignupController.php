<?php

namespace App\controllers;

use App\exceptions\UserAlreadyExists;
use App\http\Response;
use App\requests\SignUpRequest;
use Psr\Http\Message\ServerRequestInterface;
use React\MySQL\ConnectionInterface;
use React\MySQL\Exception;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

class SignupController
{
    public function __construct(
        private ConnectionInterface $connection
    ) {}
    public function signup(SignUpRequest $server): PromiseInterface
    {
        $email = $server->getParsedBody()['email'];
        $password = password_hash($server->getParsedBody()['password'], PASSWORD_BCRYPT);

        return $this->emailIsRegistered($email)
            ->then(
                function () use ($email, $password) {
                    return $this->connection
                        ->query(
                            'INSERT INTO users (email, password) VALUES (?, ?)',
                            [$email, $password]
                        )->then(
                            function (QueryResult $result) {
                                return Response::created([]);
                            },
                            function (\Throwable $throwable) {
                                return response::internalServerError($throwable->getMessage());
                            }
                        );
                }
            )->catch(function (UserAlreadyExists $exception) {
                return Response::badRequest($exception->getMessage());
            })->catch(function (\Throwable $throwable) {
                return response::internalServerError($throwable->getMessage());
            });
    }

    private function emailIsRegistered(string $email): PromiseInterface
    {
        return $this->connection
            ->query(
                "SELECT * FROM users WHERE email = (?)",
                [$email]
            )->then(
                function (QueryResult $result) use ($email) {
                   if($result->resultRows > 0) {
                       throw new UserAlreadyExists("Email {$email} já foi registrado");
                   }
                }
            );
    }
}