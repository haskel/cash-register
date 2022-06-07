<?php

declare(strict_types=1);

namespace App\Service\Receipt;

use App\Entity\CashRegister;
use App\Entity\Receipt;
use App\Enum\ReceiptStatus;
use App\Exception\FinishedReceiptUpdateException;
use App\Exception\InexistentProductInReceiptException;
use App\Exception\NotFoundException;
use App\Exception\NotImplementedException;
use App\Exception\UserHaveNoCashRegisterException;
use App\Exception\ValidationException;
use App\Repository\CashRegisterRepository;
use App\Repository\ProductRepository;
use App\Repository\ReceiptRepository;
use App\Service\Product\Identifier\ProductCompositeId;
use App\Service\VatCalculator;
use DateTimeImmutable;
use App\Dto\Receipt as ReceiptDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReceiptService
{
    private CashRegister $cashRegister;

    public function __construct(
        private readonly ReceiptRepository $receiptRepository,
        private readonly CashRegisterRepository $cashRegisterRepository,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly VatCalculator $vatCalculator,
    ) {
        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user) {
            throw new AccessDeniedException();
        }

        $cashRegister = $this->cashRegisterRepository->findByUser($user);
        if (!$cashRegister) {
            throw new UserHaveNoCashRegisterException();
        }
        $this->cashRegister = $cashRegister;
    }

    public function create(): ReceiptDto
    {
        $receipt = new Receipt($this->cashRegister);

        $errorsList = $this->validator->validate($receipt);
        if ($errorsList->count()) {
            throw new ValidationException('Receipt is invalid', $errorsList);
        }

        $this->entityManager->persist($receipt);
        $this->entityManager->flush();

        return ReceiptDto::fromEntity($receipt);
    }

    public function update(): void
    {
        throw new NotImplementedException();
    }

    public function get(int $receiptId): ReceiptDto
    {
        $receipt = $this->findReceipt($receiptId);
        if (!$receipt) {
            throw new NotFoundException('Receipt not found', ['id' => $receiptId]);
        }

        return ReceiptDto::fromEntity($receipt);
    }

    public function find(int $receiptId): ?ReceiptDto
    {
        $receipt = $this->findReceipt($receiptId);

        return $receipt ? ReceiptDto::fromEntity($receipt) : null;
    }

    private function findReceipt(int $receiptId): ?Receipt
    {
        return $this->receiptRepository->find($receiptId);
    }

    private function getUnfinished(int $receiptId): Receipt
    {
        $receipt = $this->findReceipt($receiptId);

        if (!$receipt) {
            throw new NotFoundException('Receipt not found', ['id' => $receiptId]);
        }

        if ($receipt->isFinished()) {
            throw new FinishedReceiptUpdateException($receipt->getId());
        }

        return $receipt;
    }

    public function delete(int $receiptId): void
    {
        throw new NotImplementedException();
    }

    /**
     * @return Receipt[]
     */
    public function filter(): array
    {
        return $this->receiptRepository->findBy([]);
    }

    public function addProduct(int $receiptId, ProductCompositeId $productCompositeId): ReceiptDto
    {
        $receipt = $this->getUnfinished($receiptId);

        $product = $this->productRepository->findByBarcode($productCompositeId->getValue());
        if (!$product) {
            throw new NotFoundException('Product not found', ['barcode' => $productCompositeId->getValue()]);
        }

        $receipt->addProduct($product);

        $errorsList = $this->validator->validate($receipt);
        if ($errorsList->count()) {
            throw new ValidationException('Receipt is invalid', $errorsList);
        }

        $this->entityManager->persist($receipt);
        $this->entityManager->flush();

        return ReceiptDto::fromEntity($receipt);
    }

    public function removeProduct(int $receiptId, ProductCompositeId $productCompositeId): ReceiptDto
    {
        $receipt = $this->getUnfinished($receiptId);

        $product = $this->productRepository->findByBarcode($productCompositeId->getValue());
        if (!$product) {
            throw new NotFoundException('Product not found', ['barcode' => $productCompositeId->getValue()]);
        }

        $receipt->removeProduct($product);

        $errorsList = $this->validator->validate($receipt);
        if ($errorsList->count()) {
            throw new ValidationException('Receipt is invalid', $errorsList);
        }

        $this->entityManager->persist($receipt);
        $this->entityManager->flush();

        return ReceiptDto::fromEntity($receipt);
    }

    public function finish(int $receiptId): ReceiptDto
    {
        $receipt = $this->findReceipt($receiptId);
        if (!$receipt) {
            throw new NotFoundException('Receipt not found', ['id' => $receiptId]);
        }

        if ($receipt->isFinished()) {
            return ReceiptDto::fromEntity($receipt);
        }

        $receipt->setStatus(ReceiptStatus::Finished);
        $receipt->setFinishedAt(new DateTimeImmutable());

        foreach ($receipt->getRows() as $row) {
            $product = $this->productRepository->find($row->getProductId());
            if (!$product) {
                throw new InexistentProductInReceiptException($row->getProductId());
            }

            $row->setProductName($product->getName());
            $row->setPrice($product->getPrice());
            $row->setVatClass($product->getVatClass());
            $row->setVatPercent($this->vatCalculator->getPercentByClass($product->getVatClass()));
            $row->setVatAmount($this->vatCalculator->calculate($product) * $row->getAmount());
        }

        $errorsList = $this->validator->validate($receipt);
        if ($errorsList->count()) {
            throw new ValidationException('Receipt is invalid', $errorsList);
        }

        $this->entityManager->persist($receipt);
        $this->entityManager->flush();

        return ReceiptDto::fromEntity($receipt);
    }

    public function updateLastRowAmount(int $receiptId, int $amount): ReceiptDto
    {
        $receipt = $this->getUnfinished($receiptId);

        $receipt->updateLastRowAmount($amount);

        $this->entityManager->persist($receipt);
        $this->entityManager->flush();

        return ReceiptDto::fromEntity($receipt);
    }
}
