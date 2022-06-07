<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Enum\VatClass;
use App\Exception\InvalidArgumentException;
use App\Exception\UnexpectedValueException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class VatCalculator
{
    /**
     * @var array<int, scalar> $table
     */
    private array $table = [];

    /** @param array<int, scalar> $table */
    public function __construct(#[Autowire('%app.vat_class%')] array $table = [])
    {
        foreach ($table as $vatClass => $percent) {
            if (!is_int($vatClass) || $vatClass < 0 || !VatClass::tryFrom($vatClass)) {
                throw new UnexpectedValueException(sprintf('Unknown VatClass [value=%s]', $vatClass));
            }

            if (!is_numeric($percent)) {
                throw new UnexpectedValueException(sprintf('Percent should be a numeric type, %s given', $percent));
            }

            if ($percent < 0 || $percent > 100) {
                throw new UnexpectedValueException(sprintf('Percent should be between 0 and 100, %f given', $percent));
            }
        }

        $this->table = $table;
    }

    public function getPercentByClass(VatClass $vatClass): float
    {
        $amount = $this->table[$vatClass->value] ?? null;

        if ($amount === null) {
            throw new InvalidArgumentException(sprintf('Unknown percent for VAT class %s', $vatClass->name));
        }

        return (float) $amount;
    }

    public function calculate(Product $product): float
    {
        $percent = $this->getPercentByClass($product->getVatClass());

        return $product->getPrice() * $percent / 100;
    }
}
