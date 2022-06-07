<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\VatClass;
use App\Exception\InvalidArgumentException;
use App\Repository\ReceiptRepository;
use App\Validator\ExistentProduct;
use App\Validator\ValidVatClass;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Component\Validator\Constraints\Range;

#[Entity(repositoryClass: ReceiptRepository::class)]
#[UniqueConstraint(fields: ['receipt', 'position'])]
#[UniqueConstraint(fields: ['receipt', 'productId'])]
class ReceiptRow
{
    #[Id, GeneratedValue(strategy: 'IDENTITY')]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[ManyToOne(targetEntity: Receipt::class, inversedBy: 'rows')]
    #[JoinColumn(name: 'receipt_id', referencedColumnName: 'id', nullable: false)]
    private Receipt $receipt;

    #[Positive]
    #[Column(type: Types::INTEGER, nullable: false)]
    private int $position;

    #[NotNull]
    #[ExistentProduct]
    #[Column(type: Types::INTEGER, nullable: false)]
    private int $productId;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 1024, nullable: false)]
    private string $productName;

    #[PositiveOrZero]
    #[Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $price;

    #[ValidVatClass]
    #[Column(type: Types::SMALLINT, nullable: false)]
    private int $vatClass;

    #[Range(notInRangeMessage: 'The percent should be between {{  min }} and {{ max }}', min: 0, max: 100)]
    #[Column(type: Types::DECIMAL, precision: 3, scale: 2, nullable: false, options: ['default' => 0])]
    private string $vatPercent;

    #[PositiveOrZero]
    #[Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: false, options: ['default' => 0])]
    private string $vatAmount;

    #[Positive]
    #[Column(type: Types::INTEGER)]
    private int $amount = 1;

    public function __construct(
        Receipt $receipt,
        int $position,
        int $productId,
        string $productName,
        float $price,
        VatClass $vatClass,
        ?int $amount = null
    ) {
        if ($position <= 0) {
            throw new InvalidArgumentException('Receipt row position must be greater than 0');
        }

        $this->receipt = $receipt;
        $this->position = $position;
        $this->productId = $productId;
        $this->productName = $productName;
        $this->setPrice($price);
        $this->vatClass = $vatClass->value;
        $this->setVatPercent(0);
        $this->setVatAmount(0);

        if (null !== $amount) {
            $this->amount = $amount;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getReceipt(): Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(Receipt $receipt): ReceiptRow
    {
        $this->receipt = $receipt;

        return $this;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): ReceiptRow
    {
        $this->productName = $productName;

        return $this;
    }

    public function getPrice(): float
    {
        return (float) $this->price;
    }

    public function setPrice(float $price): ReceiptRow
    {
        $this->price = sprintf('%0.2F', $price);

        return $this;
    }

    public function increment(): void
    {
        ++$this->amount;
    }

    public function decrement(): void
    {
        --$this->amount;

        if ($this->amount < 0) {
            $this->amount = 0;
        }
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Receipt row amount must be greater than 0');
        }

        $this->amount = $amount;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;

        return $this;
    }

    public function getVatClass(): VatClass
    {
        return VatClass::from($this->vatClass);
    }

    public function setVatClass(VatClass $vatClass): self
    {
        $this->vatClass = $vatClass->value;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        if ($position <= 0) {
            throw new InvalidArgumentException('Receipt row position must be greater than 0');
        }

        $this->position = $position;

        return $this;
    }

    public function setVatPercent(float $vatPercent): ReceiptRow
    {
        $this->vatPercent = sprintf('%0.2F', $vatPercent);

        return $this;
    }

    public function getVatPercent(): float
    {
        return (float) $this->vatPercent;
    }

    public function setVatAmount(float $vatAmount): ReceiptRow
    {
        $this->vatAmount = sprintf('%0.2F', $vatAmount);

        return $this;
    }

    public function getVatAmount(): float
    {
        return (float) $this->vatAmount;
    }
}
