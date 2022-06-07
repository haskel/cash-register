<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ValidVatClass extends Constraint
{
    public string $message = 'Unknown VAT class [value={{ number }}]';
}
