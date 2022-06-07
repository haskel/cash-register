<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ValidReceiptStatus extends Constraint
{
    public string $message = 'Unknown receipt status [value={{ number }}]';
}
