<?php

namespace App\http;

use App\container\Container;
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

        $routeInfo = $this->extractRouteInfo($serverRequest);

        if(!is_array($routeInfo)) {
            return $routeInfo;
        }

        [$handler, $vars] = $routeInfo;

        if(is_array($handler)) {
            [$controllerId, $method] = $handler;

            $controller = Container::getInstance()->get($controllerId);
            $handler = [$controller, $method];
            $vars = $this->autoWireMethod($handler, $vars, $serverRequest);
            return call_user_func_array($handler, $vars);
        }
    }

    private function extractRouteInfo(ServerRequestInterface $serverRequest): Response|array
    {
        $routeInfo = $this->dispatcher->dispatch(
            $serverRequest->getMethod(), $serverRequest->getUri()->getPath()
        );

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new Response(404, ['Content-type' => 'text/plain'], json_encode('Not found'));
            case Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(405, ['Content-type' => 'text/plain'], json_encode('Method not allowed'));
            case Dispatcher::FOUND:
                return [$routeInfo[1], $routeInfo[2]];
            default:
                throw new LogicException('Something went wrong with routing');
        }
    }

    private function autoWireMethod($handler, $vars, ServerRequestInterface $serverRequest)
    {
        $reflectionController = new ReflectionClass($handler[0]);
        $reflectionMethod = $reflectionController->getMethod($handler[1]);
        foreach($reflectionMethod->getParameters() as $parameter) {
            if(array_key_exists($parameter->name, $vars)) {
                continue;
            }

            if($parameter->getType()->getName() === 'Psr\Http\Message\ServerRequestInterface') {
                $vars[$parameter->name] = $serverRequest;
                continue;
            }

            $vars[$parameter->name] = Container::getInstance()->get($parameter->getType()->getName());
        }
        return $vars;
    }
}