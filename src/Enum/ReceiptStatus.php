<?php

declare(strict_types=1);

namespace App\Enum;

enum ReceiptStatus: int
{
    case InProgress = 1;
    case Finished = 2;
}
