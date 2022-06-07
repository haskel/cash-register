<?php

namespace App\Dto;

class ValidationError
{
    public string $message;

    public string $field;

    public function __construct(string $message, string $field)
    {
        $this->message = $message;
        $this->field = $field;
    }
}
