<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Product;
use App\Entity\Product as ProductEntity;
use App\Enum\VatClass;
use App\Exception\InvalidArgumentException;
use App\Exception\NotImplementedException;
use App\Service\Product\Identifier\ProductCompositeId;
use App\Service\Product\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
#[AsController]
class ProductController
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    #[Route(methods: [Request::METHOD_POST])]
    public function create(Request $request): Product
    {
        $barcode = $request->request->getAlnum('barcode');
        $name = $request->request->getAlnum('name');
        $price = (float) $request->request->get('price');
        $vatClass = VatClass::tryFrom((int) $request->request->get('vatClass'));
        if (!$vatClass) {
            throw new InvalidArgumentException('Invalid VAT class');
        }

        return $this->productService->create($barcode, $name, $price, $vatClass);
    }

    #[Route('/{compositeId}', methods: [Request::METHOD_GET])]
    public function read(string $compositeId): Product
    {
        $id = new ProductCompositeId($compositeId);

        return $this->productService->get($id);
    }

    #[Route('/{compositeId}', methods: [Request::METHOD_PUT])]
    public function update(string $compositeId, Request $request): Product
    {
        throw new NotImplementedException();
    }

    #[Route('/{compositeId}', methods: [Request::METHOD_DELETE])]
    public function delete(string $compositeId): void
    {
        $id = new ProductCompositeId($compositeId);

        $this->productService->delete($id);
    }

    /**
     * @return Product[]
     */
    #[Route(methods: [Request::METHOD_GET])]
    public function list(): array
    {
        return $this->productService->filter();
    }
}
