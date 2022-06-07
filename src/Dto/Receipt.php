<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Receipt as ReceiptEntity;
use App\Entity\ReceiptRow as ReceiptRowEntity;

class Receipt
{
    /**
     * @param ReceiptRow[] $rows
     */
    public function __construct(
        public readonly int $id,
        public readonly string $createdAt,
        public readonly array $rows,
        public readonly bool $isFinished,
        public readonly float $total,
    ) {
    }

    public static function fromEntity(ReceiptEntity $entity): self
    {
        $rows = array_map(
            static fn (ReceiptRowEntity $rowEntity) => ReceiptRow::fromEntity($rowEntity),
            $entity->getRows()->toArray()
        );

        $total = array_reduce(
            $entity->getRows()->toArray(),
            static fn (float $value, ReceiptRowEntity $rowEntity) => $value + ($rowEntity->getPrice() * $rowEntity->getAmount()),
            0
        );

        return new self(
            $entity->getId(),
            $entity->getCreatedAt()->format(\DATE_ATOM),
            $rows,
            $entity->isFinished(),
            ceil($total * 100) / 100
        );
    }
}
