<?php

declare(strict_types=1);

namespace App\Report\TurnoverPerHour;

use App\Report\BaseReportEvent;

class TurnoverPerHourEvent extends BaseReportEvent
{
    private TurnoverPerHourResultDto $result;

    public function getResult(): ?TurnoverPerHourResultDto
    {
        return $this->result ?? null;
    }

    public function setResult(TurnoverPerHourResultDto $result): void
    {
        $this->result = $result;
    }
}
