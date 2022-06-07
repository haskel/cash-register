<?php

declare(strict_types=1);

namespace App\Service\Product;

use App\Entity\Product;
use App\Dto\Product as ProductDto;
use App\Enum\VatClass;
use App\Exception\NotFoundException;
use App\Exception\NotImplementedException;
use App\Exception\ValidationException;
use App\Repository\ProductRepository;
use App\Service\Product\Identifier\ProductCompositeId;
use App\Service\Product\Identifier\ProductIdType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function create(string $barcode, string $name, float $price, VatClass $vatClass): ProductDto
    {
        $product = new Product($barcode, $name, $price, $vatClass);

        $errorsList = $this->validator->validate($product);
        if ($errorsList->count()) {
            throw new ValidationException('Product is invalid', $errorsList);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return ProductDto::fromEntity($product);
    }

    public function update(): ProductDto
    {
        throw new NotImplementedException();
    }

    public function get(ProductCompositeId $compositeId): ProductDto
    {
        if (!$product = $this->doFind($compositeId)) {
            throw new NotFoundException('Product not found', ['id' => $compositeId->getOriginal()]);
        }

        return ProductDto::fromEntity($product);
    }

    public function find(ProductCompositeId $compositeId): ?ProductDto
    {
        $product = $this->doFind($compositeId);

        return $product ? ProductDto::fromEntity($product) : null;
    }

    private function doFind(ProductCompositeId $compositeId): ?Product
    {
        return match ($compositeId->getType()) {
            ProductIdType::Id => $this->productRepository->find((int) $compositeId->getValue()),
            ProductIdType::Barcode => $this->productRepository->findByBarcode($compositeId->getValue()),
        };
    }

    public function delete(ProductCompositeId $compositeId): void
    {
        if (!$product = $this->find($compositeId)) {
            return;
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    /**
     * @return ProductDto[]
     */
    public function filter(): array
    {
        $products = $this->productRepository->findAll() ?: [];

        return array_map(
            static fn (Product $product) => ProductDto::fromEntity($product),
            $products
        );
    }
}
