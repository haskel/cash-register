<?php

declare(strict_types=1);

namespace App\Report\TurnoverPerHour;

use App\Constant\PostgresDateFormat;
use App\Enum\ReceiptStatus;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

class Query
{
    public function __construct(private Connection $connection)
    {
    }

    /**
     * @return array<array<string, string|float>>
     * @throws \Doctrine\DBAL\Exception
     */
    public function execute(DateTimeImmutable $startDate): array
    {
        return $this->connection->fetchAllAssociative(
            "
            select 
                rr.product_name as name,
                sum(rr.price * rr.amount) as amount
            from
                receipt_row rr
            inner join receipt r on rr.receipt_id = r.id
            where
                r.status = :finishStatus
                and r.finished_at between date_trunc('hour', current_timestamp) and date_trunc('hour', current_timestamp + interval '1 hour')
            group by rr.product_id, rr.product_name
        ",
            [
                'finishStatus' => ReceiptStatus::Finished->value,
//                'startDate' => $startDate->format(PostgresDateFormat::DATETIME),
            ]
        );
    }
}
