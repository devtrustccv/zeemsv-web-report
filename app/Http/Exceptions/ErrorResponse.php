<?php

namespace App\Exceptions;

use Exception;

class ErrorResponse extends Exception
{
    protected $message;
    protected $code;

    public function __construct(string $message = "Erro genérico", int $code = 500)
    {
        parent::__construct($message, $code);
        $this->message = $message;
        $this->code = $code;
    }

    public static function serverError(string $message = 'Erro no servidor'): self
    {
        return new self($message, 500);
    }

    public function toJson(): string
    {
        return json_encode([
            'error' => true,
            'message' => $this->message,
            'code' => $this->code,
        ]);
    }
}
