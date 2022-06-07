<?php

declare(strict_types=1);

namespace App\Service\Idempotency\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class IdempotentRequest
{
}
