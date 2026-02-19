<?php

namespace App\Exceptions;

use Exception;

class DatabaseException extends Exception
{
    protected $operation;
    protected $context;

    public function __construct(string $message, string $operation = 'database_operation', array $context = [], Exception $previous = null)
    {
        parent::__construct($message, 500, $previous);
        $this->operation = $operation;
        $this->context = $context;
    }

    public function getOperation(): string
    {
        return $this->operation;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function render($request)
    {
        \Log::error('Database operation failed', [
            'operation' => $this->operation,
            'message' => $this->getMessage(),
            'context' => $this->context,
            'trace' => $this->getTraceAsString(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error_code' => 'DATABASE_ERROR',
                'message' => 'A database error occurred. Please try again later.',
                'debug' => config('app.debug') ? $this->getMessage() : null,
            ], 500);
        }

        return redirect()->back()
            ->with('error', 'A system error occurred. Please try again later.')
            ->withInput();
    }
}
