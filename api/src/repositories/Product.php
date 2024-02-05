<?php

namespace App\repositories;

use App\entitys\Product as ProductEntity;
use App\exceptions\ProductNotFound;
use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;

class Product
{
    public function __construct(
        private ConnectionInterface $connection
    ) {}

    /*public function findOrFail(): ProductEntity
    {

    }*/

    public function getById(int $id): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT * from products WHERE id = ?',
                [$id]
            )->then(function (QueryResult $result) {
                if(empty($result->resultRows)) {
                    throw new ProductNotFound();
                }
                $row = $result->resultRows[0];
                return new ProductEntity(
                    id: (int)$row['id'],
                    name: $row['name'],
                    price: (float)$row['price']
                );
            });
    }
}