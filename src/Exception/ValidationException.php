<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends AppPublicException
{
    private ConstraintViolationListInterface $errors;

    public function __construct(string $message, ConstraintViolationListInterface $constraintViolationList)
    {
        parent::__construct($message);

        $this->errors = $constraintViolationList;
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
