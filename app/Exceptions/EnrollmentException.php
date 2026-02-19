<?php

namespace App\Exceptions;

use Exception;

class EnrollmentException extends Exception
{
    protected $errorCode;
    protected $context;

    public function __construct(string $message, string $errorCode = 'ENROLLMENT_ERROR', array $context = [], int $code = 400)
    {
        parent::__construct($message, $code);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error_code' => $this->errorCode,
                'message' => $this->getMessage(),
                'context' => $this->context,
            ], $this->getCode());
        }

        return redirect()->back()
            ->with('error', $this->getMessage())
            ->withInput();
    }
}
