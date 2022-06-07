<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Product as ProductEntity;

class Product
{
    public function __construct(
        public readonly int $id,
        public readonly string $barcode,
        public readonly string $name,
        public readonly float $price,
        public readonly ?int $vatClass,
    ) {
    }

    public static function fromEntity(ProductEntity $entity): self
    {
        return new Product(
            $entity->getId(),
            $entity->getBarcode(),
            $entity->getName(),
            $entity->getPrice(),
            $entity->getVatClass()->value,
        );
    }
}
