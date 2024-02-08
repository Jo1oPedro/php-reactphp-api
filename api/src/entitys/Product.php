<?php

namespace App\entitys;

class Product implements \JsonSerializable
{
    public function __construct(
        private string $name,
        private string $price,
        private ?int $id = null,
        private ?string $image_path = null,
    ) {}

    public function getImagePath(): ?string
    {
        return $this->image_path;
    }

    public function setImagePath(string $image_path): void
    {
        $this->image_path = $image_path;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id = null): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'image_path' => $this->getImagePath(),
            'request' => [
                'type' => 'GET',
                'url' => 'http://localhost:9292/products/' . $this->getId()
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'image_path' => $this->getImagePath()
        ];
    }

    public static function mapProduct(array $row): Product
    {
        return new Product(id:$row['id'], name:$row['name'], price:$row['price'], image_path: $row['image_path']);
    }
}