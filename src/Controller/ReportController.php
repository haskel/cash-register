<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ReportResult;
use App\Report\TurnoverPerHour\TurnoverPerHourEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/report', methods: [Request::METHOD_GET])]
class ReportController
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    #[Route('/total-turnover-per-hour')]
    public function totalTurnoverPerHour(): ReportResult
    {
        $event = new TurnoverPerHourEvent();
        $this->eventDispatcher->dispatch($event);

        return new ReportResult(
            $event->getRequestId(),
            $event->getResult(),
            $event->getResult() !== null
        );
    }
}
