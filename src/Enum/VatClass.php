<?php

declare(strict_types=1);

namespace App\Enum;

enum VatClass: int
{
    case Zero = 1;
    case Standard = 2;
    case Reduced = 3;
}
