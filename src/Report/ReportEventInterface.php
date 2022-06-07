<?php

declare(strict_types=1);

namespace App\Report;

interface ReportEventInterface
{
    public function getResult(): ?object;
    public function getRequestId(): string;
}
