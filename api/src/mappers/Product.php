<?php

namespace App\mappers;

use App\entitys\Product as ProductEntity;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

class Product
{
    public function __construct(
        private ConnectionInterface $connection
    ) {}

    public function save(ProductEntity $product): PromiseInterface
    {
        return $this->connection
            ->query(
                'INSERT INTO products (name, price) VALUES (?, ?)',
                [$product->getName(), $product->getPrice()]
            )
            ->then(function (QueryResult $result) use ($product) {
                return new ProductEntity(
                    id:$result->insertId,
                    name:$product->getName(),
                    price:$product->getPrice()
                );
            });
    }
}