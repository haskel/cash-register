<?php

declare(strict_types=1);

namespace App\Exception;

class FinishedReceiptUpdateException extends AppPublicException
{
    public function __construct(private int $receiptId)
    {
        parent::__construct();
    }

    public function getReceiptId(): int
    {
        return $this->receiptId;
    }
}
