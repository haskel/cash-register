<?php

declare(strict_types=1);

namespace App\Enum;

enum ReceiptPatchAction: string
{
    case IncrementRow = 'incrementAmount';
    case DecrementRow = 'decrementAmount';
    case UpdateRowAmount = 'updateRowAmount';
}
