<?php

declare(strict_types=1);

namespace App\Exception;

class NotFoundException extends AppPublicException
{
    /**
     * @var array<string, mixed>
     */
    protected array $context = [];

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(string $message, array $context = [])
    {
        parent::__construct($message);

        $this->context = $context;
    }
}
