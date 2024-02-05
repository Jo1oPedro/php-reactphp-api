<?php

namespace App\controllers;

use App\entitys\Product as ProductEntity;
use App\exceptions\ProductNotFound;
use App\mappers\Product as ProductMapper;
use App\repositories\Product as ProductRepository;
use App\rabbitmq\RabbitmqManager;
use Psr\Http\Message\ServerRequestInterface;
//use React\Http\Message\Response;
use App\http\Response;
class ProductController extends Controller
{
    public function __construct(
        private ProductMapper $productMapper,
        private ProductRepository $productRepository
    ) {}

    public function index(ServerRequestInterface $serverRequest)
    {
        RabbitmqManager::publishMessage(
            'produto_registrado',
            'mensagem produto registrado2'
        );

        return (new Response())(
            200,
            json_encode(['message' => 'GET request to /products'])
        );
    }

    public function store(ServerRequestInterface $request)
    {
        $name = $request->getParsedBody()['name'];
        $price =$request->getParsedBody()['price'];

        return $this->productMapper
            ->save(new ProductEntity(name: $name, price: $price))
            ->then(
                function (ProductEntity $product) {
                    return Response::ok(json_encode($product));
                },
                function (\Exception $exception) {
                    return Response::internalServerError($exception->getMessage());
                }
            );

        /*return Response::ok(
            json_encode([
                'message' => 'POST request to /products',
                'product' => $product
            ]),
        );*/
    }

    public function show(int $id)
    {
        return $this->productRepository->getById($id)
            ->then(
                function (ProductEntity $product) {
                    return Response::ok(json_encode($product));
                },
            )->catch(function (ProductNotFound $productNotFound) {
                return Response::notFound();
            })->catch(function (\Exception $exception) {
                return Response::internalServerError($exception->getMessage());
            });
    }

    public function update(int $id)
    {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => "PUT request to /products/{$id}"])
        );
    }

    public function delete(int $id)
    {
        return new Response(
            200,
            ['Content-type' => 'application/json'],
            json_encode(['message' => "DELETE request to /products/{$id}"])
        );
    }
}