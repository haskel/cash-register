<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\ProductRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ExistentProductValidator extends ConstraintValidator
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistentProduct) {
            throw new UnexpectedTypeException($constraint, ExistentProduct::class);
        }

        if (null === $value) {
            return;
        }

        if (!is_int($value)) {
            throw new UnexpectedValueException($value, 'integer');
        }

        $product = $this->productRepository->find($value);
        if (!$product) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ id }}', (string) $value)
                ->addViolation();
        }
    }
}
