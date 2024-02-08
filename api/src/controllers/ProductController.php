<?php

namespace App\controllers;

use App\entitys\Product;
use App\exceptions\ProductNotFound;
use App\mappers\Product as ProductMapper;
use App\repositories\Product as ProductRepository;
use App\rabbitmq\RabbitmqManager;
use App\requests\ProductCreateRequest;
use App\uploader\File;
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
        private ConnectionInterface $connection,
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
                    $response = [
                        'products' => array_map(function (array $product) {
                            return Product::mapProduct($product);
                        }, $result->resultRows),
                        'count' => count($result->resultRows)
                    ];
                    return Response::ok($response);
                },
                function (\Exception $exception) {
                    return Response::internalServerError($exception->getMessage());
                }
            );
    }

    public function store(ProductCreateRequest $request): PromiseInterface
    {
        $name = $request->getParsedBody()['name'];
        $price = $request->getParsedBody()['price'];
        $image_path = File::upload($request->getRequest());

        return $this->productMapper
            ->save(new Product(name: $name, price: $price, image_path: $image_path))
            ->then(
                function (Product $product) {
                    return Response::ok($product);
                },
                function (\Exception $exception) {
                    return Response::internalServerError($exception->getMessage());
                }
            );
    }

    public function show(int $id): PromiseInterface
    {
        return $this->productRepository->getById($id)
            ->then(
                function (Product $product) {
                    return Response::ok($product);
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