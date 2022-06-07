<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CashRegisterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Unique;

#[Entity(repositoryClass: CashRegisterRepository::class)]
class CashRegister
{
    #[Id, GeneratedValue(strategy: 'IDENTITY')]
    #[Column(type: Types::INTEGER)]
    private int $id;

    #[Column(type: Types::STRING, length: 1024, nullable: true)]
    private ?string $name;

    #[NotBlank]
    #[Unique]
    #[Column(type: Types::STRING, length: 1024, unique: true, nullable: false)]
    private string $serial;

    #[NotBlank]
    #[Column(type: Types::STRING, length: 1024, nullable: false)]
    private string $bindToUser;

    /** @var ArrayCollection<int, Receipt>  */
    #[OneToMany(mappedBy: 'cashRegister', targetEntity: Receipt::class)]
    private iterable $receipts;

    public function __construct(string $serial, string $bindToUser, ?string $name = null)
    {
        $this->serial = $serial;
        $this->bindToUser = $bindToUser;
        $this->name = $name;
        $this->receipts = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /** @return ArrayCollection<int, Receipt> */
    public function getReceipts(): iterable|ArrayCollection
    {
        return $this->receipts;
    }

    public function setSerial(string $serial): CashRegister
    {
        $this->serial = $serial;

        return $this;
    }

    public function getSerial(): string
    {
        return $this->serial;
    }

    public function setBindToUser(string $bindToUser): CashRegister
    {
        $this->bindToUser = $bindToUser;

        return $this;
    }

    public function getBindToUser(): string
    {
        return $this->bindToUser;
    }
}
