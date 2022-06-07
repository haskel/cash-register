<?php

declare(strict_types=1);

namespace App\Console;

use App\Entity\CashRegister;
use App\Repository\CashRegisterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:cash-register:create', 'Add new cash register to the database')]
class CashRegisterCreateCommand extends Command
{
    public function __construct(
        private CashRegisterRepository $cashRegisterRepository,
        private EntityManagerInterface $entityManager,
        string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('serial', InputArgument::REQUIRED, 'Cash register serial number')
            ->addArgument('user', InputArgument::REQUIRED, 'Cash register bind user')
            ->addOption('name', '', InputOption::VALUE_OPTIONAL, 'Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $serial = $input->getArgument('serial');
        if ('' === trim($serial)) {
            $output->writeln('<error>Serial number cann\'t be empty</error>');

            return self::FAILURE;
        }

        $bindUser = $input->getArgument('user');
        if ('' === trim($bindUser)) {
            $output->writeln('<error>User cann\'t be empty</error>');

            return self::FAILURE;
        }

        $name = $input->getOption('name');

        $exitsCashRegister = $this->cashRegisterRepository->findBy(['serial' => $serial]);
        if ($exitsCashRegister) {
            $output->writeln('<error>Cash register with the same serial number already exits</error>');

            return self::FAILURE;
        }

        $cashRegister = new CashRegister($serial, $bindUser, $name);
        $this->entityManager->persist($cashRegister);
        $this->entityManager->flush();

        $output->writeln('<info>Cash register successfully created</info>');

        return self::SUCCESS;
    }
}
