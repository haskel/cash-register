<?php

declare(strict_types=1);

namespace App\Report\TurnoverPerHour;

class ProductTotalDto
{
    public readonly string $name;
    public readonly float $total;

    public function __construct(string $name, float $total)
    {
        $this->name = $name;
        $this->total = $total;
    }
}
