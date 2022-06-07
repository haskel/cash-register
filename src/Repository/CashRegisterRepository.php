<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CashRegister;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<CashRegister>
 */
class CashRegisterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CashRegister::class);
    }

    public function findByUser(UserInterface $user): ?CashRegister
    {
        return $this->findOneBy(['bindToUser' => $user->getUserIdentifier()]);
    }
}
