<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Receipt;
use App\Enum\ReceiptAction;
use App\Enum\ReceiptPatchAction;
use App\Exception\InvalidArgumentException;
use App\Exception\UnexpectedValueException;
use App\Service\Idempotency\Attribute\IdempotentRequest;
use App\Service\Product\Identifier\ProductCompositeId;
use App\Service\Receipt\ReceiptService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cash-register/receipt')]
#[AsController]
class ReceiptController
{
    public function __construct(private readonly ReceiptService $receiptService)
    {
    }

    #[Route(methods: Request::METHOD_POST)]
    public function create(): Receipt
    {
        return $this->receiptService->create();
    }

    #[Route('/{receiptId}', methods: Request::METHOD_GET)]
    public function read(int $receiptId): Receipt
    {
        return $this->receiptService->get($receiptId);
    }

    #[Route('/{receiptId}', methods: Request::METHOD_PUT)]
    public function update(int $receiptId, Request $request): Receipt
    {
        $action = $request->request->get('action');

        if ($action === ReceiptAction::Finish->value) {
            return $this->receiptService->finish($receiptId);
        }

        return $this->receiptService->get($receiptId);
    }

    #[IdempotentRequest]
    #[Route('/{receiptId}/row/{barcode}', methods: Request::METHOD_PATCH)]
    public function changeProductAmount(int $receiptId, string $barcode, Request $request): Receipt
    {
        $productId = ProductCompositeId::fromBarcode($barcode);

        $action = ReceiptPatchAction::tryFrom($request->request->getAlnum('action'));

        return match ($action) {
            ReceiptPatchAction::IncrementRow => $this->receiptService->addProduct($receiptId, $productId),
            ReceiptPatchAction::DecrementRow => $this->receiptService->removeProduct($receiptId, $productId),
            default => throw new UnexpectedValueException('Action is not supported'),
        };
    }

    #[IdempotentRequest]
    #[Route('/{receiptId}/row', methods: Request::METHOD_PUT)]
    public function addProduct(int $receiptId, Request $request): Receipt
    {
        $barcode = $request->request->getAlnum('barcode');
        if ('' === trim($barcode)) {
            throw new InvalidArgumentException('Barcode can not empty');
        }

        return $this->receiptService->addProduct($receiptId, ProductCompositeId::fromBarcode($barcode));
    }

    #[IdempotentRequest]
    #[Route('/{receiptId}/row', methods: Request::METHOD_DELETE)]
    public function removeProduct(int $receiptId, Request $request): Receipt
    {
        $barcode = $request->request->getAlnum('barcode');
        if ('' === trim($barcode)) {
            throw new InvalidArgumentException('Barcode can not empty');
        }

        return $this->receiptService->removeProduct($receiptId, ProductCompositeId::fromBarcode($barcode));
    }

    #[Route('/{receiptId}/row/last', methods: Request::METHOD_PUT)]
    public function updateLastRowAmount(int $receiptId, Request $request): Receipt
    {
        $amount = $request->request->getInt('amount');
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount must be greater or equal 0');
        }

        return $this->receiptService->updateLastRowAmount($receiptId, $amount);
    }
}
