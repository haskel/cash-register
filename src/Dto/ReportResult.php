<?php

declare(strict_types=1);

namespace App\Dto;

class ReportResult
{
    public readonly string $requestId;
    public readonly ?object $result;
    public bool $completed;

    public function __construct(string $requestId, ?object $result, bool $completed = false)
    {
        $this->requestId = $requestId;
        $this->result = $result ?? null;
        $this->completed = $completed;
    }

    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
    }
}
