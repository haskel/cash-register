<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Product;
use App\Enum\VatClass;
use App\Exception\NotImplementedException;
use App\Service\Product\Identifier\ProductCompositeId;
use App\Service\Product\ProductService;
use Haskel\RequestParamBindBundle\Attribute\FromBody;
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
    public function create(
        #[FromBody] string $barcode,
        #[FromBody] string $name,
        #[FromBody] float $price,
        #[FromBody] VatClass $vatClass,
    ): Product {
        return $this->productService->create($barcode, $name, $price, $vatClass);
    }

    #[Route('/{productId}', methods: [Request::METHOD_GET])]
    public function read(string $productId): Product
    {
        $id = new ProductCompositeId($productId);

        return $this->productService->get($id);
    }

    #[Route('/{productId}', methods: [Request::METHOD_PUT])]
    public function update(string $productId, Request $request): Product
    {
        throw new NotImplementedException();
    }

    #[Route('/{productId}', methods: [Request::METHOD_DELETE])]
    public function delete(string $productId): void
    {
        $id = new ProductCompositeId($productId);

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
