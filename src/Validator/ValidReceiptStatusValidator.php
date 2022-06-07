<?php

declare(strict_types=1);

namespace App\Validator;

use App\Enum\ReceiptStatus;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidReceiptStatusValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidReceiptStatus) {
            throw new UnexpectedTypeException($constraint, ValidReceiptStatus::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_int($value)) {
            throw new UnexpectedValueException($value, 'integer');
        }

        if (!ReceiptStatus::tryFrom($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ number }}', (string) $value)
                ->addViolation();
        }
    }
}
