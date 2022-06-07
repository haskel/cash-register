<?php

declare(strict_types=1);

namespace App\Report\TurnoverPerHour;

class TurnoverPerHourResultDto
{
    /**
     * @var array<ProductTotalDto>
     */
    public array $products = [];

    public float $total = 0;

    /**
     * @param array<ProductTotalDto> $products
     */
    public function __construct(array $products, float $total)
    {
        $this->products = $products;
        $this->total = $total;
    }
}
