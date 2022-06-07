<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ExistentProduct extends Constraint
{
    public string $message = 'Product with #{{ id }} does not exist';
}
