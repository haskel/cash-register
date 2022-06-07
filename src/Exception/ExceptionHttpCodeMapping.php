<?php

declare(strict_types=1);

namespace App\Exception;

use App\Core\Http\ExceptionHttpMapInterface;
use Throwable;

class ExceptionHttpCodeMapping implements ExceptionHttpMapInterface
{
    private const MAP = [
        AppPublicException::class => 400,
        BaseException::class => 500,
        FinishedReceiptUpdateException::class => 400,
        InexistentProductInReceiptException::class => 404,
        InvalidArgumentException::class => 500,
        NotFoundException::class => 404,
        NotImplementedException::class => 500,
        UserHaveNoCashRegisterException::class => 403,
        ValidationException::class => 400,
    ];

    public function getCode(Throwable $e): ?int
    {
        return self::MAP[$e::class] ?? null;
    }
}
