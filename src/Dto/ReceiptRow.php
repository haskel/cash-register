<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\ReceiptRow as ReceiptRowEntity;

class ReceiptRow
{
    public function __construct(
        public readonly string $productName,
        public readonly float $price,
        public readonly int $amount,
        public readonly float $vatAmount
    ) {
    }

    public static function fromEntity(ReceiptRowEntity $rowEntity): self
    {
        return new self(
            $rowEntity->getProductName(),
            $rowEntity->getPrice(),
            $rowEntity->getAmount(),
            $rowEntity->getVatAmount(),
        );
    }
}
