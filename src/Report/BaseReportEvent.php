<?php

declare(strict_types=1);

namespace App\Report;

use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\Event;

abstract class BaseReportEvent extends Event implements ReportEventInterface
{
    protected Uuid $requestId;

    public function __construct()
    {
        $this->requestId = Uuid::v4();
    }

    public function getRequestId(): string
    {
        return $this->requestId->toRfc4122();
    }
}
