<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VatClass;
use App\Repository\ProductRepository;
use App\Validator\ValidVatClass;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Unique;

#[Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[Id, GeneratedValue(strategy: 'IDENTITY')]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 1024, unique: true, nullable: false)]
    private string $barcode;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 1024, nullable: false)]
    private string $name;

    #[PositiveOrZero]
    #[Column(type: Types::DECIMAL, precision: 12, scale: 2)]
    private string $price;

    #[ValidVatClass]
    #[Column(type: Types::SMALLINT, nullable: false)]
    private int $vatClass;

    public function __construct(string $barcode, string $name, float $price, VatClass $vatClass)
    {
        $this->barcode = $barcode;
        $this->name = $name;
        $this->price = sprintf('%0.2F', $price);
        $this->vatClass = $vatClass->value;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Product
    {
        $this->id = $id;

        return $this;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    public function setBarcode(string $barcode): Product
    {
        $this->barcode = $barcode;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): float
    {
        return (float) $this->price;
    }

    public function setPrice(float $price): Product
    {
        $this->price = sprintf('%0.2F', $price);

        return $this;
    }

    public function getVatClass(): VatClass
    {
        return VatClass::from($this->vatClass);
    }

    public function setVatClass(VatClass $vatClass): Product
    {
        $this->vatClass = $vatClass->value;

        return $this;
    }
}
