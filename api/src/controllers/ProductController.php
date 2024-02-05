<?php

namespace App\controllers;

use App\entitys\Product;
use App\entitys\Product as ProductEntity;
use App\exceptions\ProductNotFound;
use App\mappers\Product as ProductMapper;
use App\repositories\Product as ProductRepository;
use App\rabbitmq\RabbitmqManager;
use Psr\Http\Message\ServerRequestInterface;
//use React\Http\Message\Response;
use App\http\Response;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

class ProductController extends Controller
{
    public function __construct(
        private ProductMapper $productMapper,
        private ProductRepository $productRepository,
        private ConnectionInterface $connection
    ) {}

    public function index(ServerRequestInterface $serverRequest): PromiseInterface
    {
        RabbitmqManager::publishMessage(
            'produto_registrado',
            'mensagem produto registrado2'
        );

        return $this->connection
            ->query("SELECT * FROM products")
            ->then(
                function (QueryResult $result) {
                    return Response::ok(
                        json_encode(
                            array_map(function (array $product) {
                               return Product::mapProduct($product);
                            }, $result->resultRows)
                        )
                    );
                },
                function (\Exception $exception) {
                    return Response::internalServerError($exception->getMessage());
                }
            );
    }

    public function store(ServerRequestInterface $request): PromiseInterface
    {
        $name = $request->getParsedBody()['name'];
        $price =$request->getParsedBody()['price'];
        var_dump($name, $price);
        return $this->productMapper
            ->save(new ProductEntity(name: $name, price: $price))
            ->then(
                function (ProductEntity $product) {
                    return Response::ok($product);
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

    public function show(int $id): PromiseInterface
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

    public function update(ServerRequestInterface $request, int $id)
    {
        return $this->productRepository
            ->getById($id)
            ->then(function (Product $product) use ($request) {
                $parsedBody = $request->getParsedBody();
                return $this->productMapper
                    ->update(
                        $product->getId(),
                        $parsedBody['name'],
                        $parsedBody['price']
                    )
                    ->then(
                        function (Product $product) {
                            return Response::ok($product);
                        },
                        function (\Exception $exception) {
                            return Response::internalServerError($exception->getMessage());
                        }
                    );
            })
            ->catch(function (ProductNotFound $exception) {
                return Response::notFound();
            })
            ->catch(function (\Exception $exception) {
                return Response::internalServerError($exception->getMessage());
            });
    }

    public function delete(int $id)
    {
        return $this->connection
            ->query(
                "DELETE FROM products WHERE id = ?",
                [$id]
            )->then(
                function (QueryResult $result) {
                    if($result->affectedRows === 0) {
                        return Response::notFound();
                    }
                    return Response::noContent();
                },
                function (\Exception $exception) {
                    return Response::internalServerError($exception->getMessage());
                }
            );
    }
}