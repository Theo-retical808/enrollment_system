<?php

namespace App\Exceptions;

class ValidationException extends EnrollmentException
{
    protected $validationErrors;

    public function __construct(string $message, array $validationErrors = [], array $context = [])
    {
        parent::__construct($message, 'VALIDATION_ERROR', $context, 422);
        $this->validationErrors = $validationErrors;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error_code' => $this->errorCode,
                'message' => $this->getMessage(),
                'validation_errors' => $this->validationErrors,
                'context' => $this->context,
            ], $this->getCode());
        }

        return redirect()->back()
            ->with('error', $this->getMessage())
            ->with('validation_errors', $this->validationErrors)
            ->withInput();
    }
}
