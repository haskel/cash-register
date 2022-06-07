<?php

declare(strict_types=1);

namespace App\Service\Product\Identifier;

use App\Exception\InvalidArgumentException;

class ProductCompositeId
{
    private const DELIMITER = ':';

    private ProductIdType $type;

    private string $value;

    public function __construct(string $compositeId)
    {
        if ('' === trim($compositeId)) {
            throw new InvalidArgumentException('Id must not be empty');
        }

        $delimiterPosition = strpos($compositeId, self::DELIMITER);
        if (false === $delimiterPosition) {
            throw new InvalidArgumentException('Delimiter not found in the composite id');
        }

        $typeName = substr($compositeId, 0, $delimiterPosition);
        $type = ProductIdType::tryParse($typeName);
        if (!$type) {
            throw new InvalidArgumentException('Unknown type of composite id');
        }

        $value = substr($compositeId, $delimiterPosition + strlen(self::DELIMITER));
        if ('' === trim($value)) {
            throw new InvalidArgumentException('The value of composite id is empty');
        }

        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): ProductIdType
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getOriginal(): string
    {
        return ProductIdType::getName($this->type).self::DELIMITER.$this->value;
    }

    public static function fromBarcode(string $barcode): self
    {
        $typeName = ProductIdType::getName(ProductIdType::Barcode);

        return new self($typeName.self::DELIMITER.$barcode);
    }
}
