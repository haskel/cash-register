<?php

declare(strict_types=1);

namespace App\Exception;

class InexistentProductInReceiptException extends AppPublicException
{
    public function __construct(private int $productId)
    {
        parent::__construct();
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}
