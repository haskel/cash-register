<?php

declare(strict_types=1);

namespace App\Dto;

class ReportResult
{
    public readonly string $requestId;
    public readonly ?object $result;

    public function __construct(string $requestId, ?object $result)
    {
        $this->requestId = $requestId;
        $this->result = $result ?? null;
    }
}
