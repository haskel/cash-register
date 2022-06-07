<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ReceiptStatus;
use App\Exception\InvalidArgumentException;
use App\Repository\ReceiptRepository;
use App\Validator\ValidReceiptStatus;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use RuntimeException;

#[Entity(repositoryClass: ReceiptRepository::class)]
class Receipt
{
    #[Id, GeneratedValue(strategy: 'IDENTITY')]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[ManyToOne(targetEntity: CashRegister::class, inversedBy: 'receipts')]
    #[JoinColumn(name: 'cash_register_id', referencedColumnName: 'id', nullable: false)]
    private CashRegister $cashRegister;

    /** @var ArrayCollection<int, ReceiptRow> */
    #[OneToMany(mappedBy: 'receipt', targetEntity: ReceiptRow::class, cascade: ['all'])]
    private iterable $rows;

    #[Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: false)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $finishedAt;

    #[ValidReceiptStatus]
    #[Column(type: Types::SMALLINT, nullable: false)]
    private int $status;

    public function __construct(CashRegister $cashRegister)
    {
        $this->cashRegister = $cashRegister;
        $this->createdAt = new DateTimeImmutable();
        $this->status = ReceiptStatus::InProgress->value;
        $this->rows = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCashRegister(): CashRegister
    {
        return $this->cashRegister;
    }

    /**
     * @return ArrayCollection<int, ReceiptRow>
     */
    public function getRows(): iterable
    {
        return $this->rows;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?DateTimeImmutable $finishedAt): Receipt
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }

    public function getStatus(): ReceiptStatus
    {
        return ReceiptStatus::from($this->status);
    }

    public function setStatus(ReceiptStatus $status): Receipt
    {
        $this->status = $status->value;

        return $this;
    }

    public function isFinished(): bool
    {
        return ReceiptStatus::Finished === $this->getStatus();
    }

    public function addProduct(Product $product): void
    {
        $row = $this->findRow($product);

        if (!$row) {
            $row = new ReceiptRow(
                $this,
                $this->getMaxRowPosition() + 1,
                $product->getId(),
                $product->getName(),
                $product->getPrice(),
                $product->getVatClass()
            );
            $this->rows->add($row);

            return;
        }

        $row->increment();
    }

    public function removeProduct(Product $product): void
    {
        $row = $this->findRow($product);

        if (!$row) {
            return;
        }

        $row->decrement();

        if (0 === $row->getAmount()) {
            $this->deleteRow($row);
        }
    }

    private function findRow(Product $product): ?ReceiptRow
    {
        $rows = $this->rows->filter(fn (ReceiptRow $row) => $row->getProductId() === $product->getId());

        if ($rows->count() > 1) {
            throw new RuntimeException();
        }

        return $rows->first() ?: null;
    }

    private function getMaxRowPosition(): int
    {
        $maxPosition = 1;

        foreach ($this->getRows() as $row) {
            if ($row->getPosition() <= $maxPosition) {
                continue;
            }

            $maxPosition = $row->getPosition();
        }

        return $maxPosition;
    }

    private function getLastRow(): ?ReceiptRow
    {
        $maxPosition = 1;
        $lastRow = null;

        foreach ($this->getRows() as $row) {
            if ($row->getPosition() >= $maxPosition) {
                $lastRow = $row;
            }
        }

        return $lastRow;
    }

    private function deleteRow(ReceiptRow $row): void
    {
        $this->rows->removeElement($row);
    }

    public function updateLastRowAmount(int $amount): void
    {
        $lastRow = $this->getLastRow();

        if (!$lastRow) {
            return;
        }

        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must be greater than or equal 0');
        }

        if (0 === $amount) {
            $this->rows->removeElement($lastRow);

            return;
        }

        $lastRow->setAmount($amount);
    }
}
