<?php

declare(strict_types=1);

namespace App\Report\TurnoverPerHour;

use DateTimeImmutable;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: TurnoverPerHourEvent::class, method: 'onReportRequest')]
class ReportExecutor
{
    public function __construct(private Query $query)
    {
    }

    public function onReportRequest(TurnoverPerHourEvent $event): void
    {
        $items = $this->query->execute();

        $products = [];
        $sum = 0;
        foreach ($items as $item) {
            $products[] = new ProductTotalDto(
                (string) $item['name'],
                (float) $item['amount']
            );
            $sum += $item['amount'];
        }

        $dto = new TurnoverPerHourResultDto($products, $sum);

        $event->setResult($dto);
    }
}
