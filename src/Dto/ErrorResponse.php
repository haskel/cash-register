<?php

namespace App\Dto;

class ErrorResponse
{
    public readonly string $message;
    public readonly string $errorId;
    /** @var array<int, mixed>|null */
    public readonly ?array $trace;

    /**
     * @param array<int, mixed>|null $trace
     */
    public function __construct(
        string $message,
        string $errorId = '',
        ?array $trace = null
    ) {
        $this->message = $message;
        $this->errorId = $errorId;

        if ($trace) {
            $this->trace = $trace;
        }
    }
}
