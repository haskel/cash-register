<?php

declare(strict_types=1);

namespace App\Core\Http;

use Throwable;

interface ExceptionHttpMapInterface
{
    public function getCode(Throwable $e): ?int;
}
