<?php

namespace App\http;

use Exception;
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\GroupCountBased;
use FastRoute\RouteCollector;
use LogicException;
use Memcached;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use ReflectionClass;

final class Router
{
    private $dispatcher;
    private $x = 0;

    public function __construct(RouteCollector $routeCollector)
    {
        $this->dispatcher = new GroupCountBased($routeCollector->getData());
    }

    public function __invoke(ServerRequestInterface $serverRequest)
    {
        $memcached = new Memcached();
        $memcached->addServer('banco_de_dados_em_memoria', 10211);
        if($this->x == 0) {
            $memcached->set('x', 'dale');
        }
        file_put_contents(__DIR__ . '/x.txt', 'valido: ' . $memcached->get('x') . ' x2:' . $this->x);
        $this->x++;

        $routeInfo = $this->dispatcher->dispatch(
            $serverRequest->getMethod(), $serverRequest->getUri()->getPath()
        );

        try {
            switch ($routeInfo[0]) {
                case Dispatcher::NOT_FOUND:
                    return new Response(404, ['Content-type' => 'text/plain'], json_encode('Not found'));
                case Dispatcher::METHOD_NOT_ALLOWED:
                    return new Response(405, ['Content-type' => 'text/plain'], json_encode('Method not allowed'));
                case Dispatcher::FOUND:
                    $reflectionController = new ReflectionClass($routeInfo[1][0]);
                    $method = $reflectionController->getMethod($routeInfo[1][1]);
                    return $method->invoke($reflectionController->newInstance(), $routeInfo[2]);

                    throw new LogicException('Something went wrong with routing');
            };
        } catch (Exception $exception) {
            dd($exception);
        }
    }
}