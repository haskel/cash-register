<?php

declare(strict_types=1);

namespace App\Service\Product\Identifier;

enum ProductIdType: int
{
    case Id = 1;
    case Barcode = 2;
    private const TYPE_BARCODE = 'barcode';
    private const TYPE_ID = 'id';

    public static function tryParse(string $name): ?self
    {
        return match ($name) {
            self::TYPE_ID => self::Id,
            self::TYPE_BARCODE => self::Barcode,
            default => null
        };
    }

    public static function getName(ProductIdType $type): ?string
    {
        return match ($type) {
            self::Id => self::TYPE_ID,
            self::Barcode => self::TYPE_BARCODE,
        };
    }
}
