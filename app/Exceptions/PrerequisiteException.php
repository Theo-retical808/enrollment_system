<?php

namespace App\Exceptions;

class PrerequisiteException extends ValidationException
{
    public function __construct(string $courseName, array $missingPrerequisites)
    {
        $message = "Cannot enroll in {$courseName}. Missing prerequisites: " . 
                   implode(', ', array_column($missingPrerequisites, 'course_code'));
        
        parent::__construct(
            $message,
            $missingPrerequisites,
            [
                'course' => $courseName,
                'missing_prerequisites' => $missingPrerequisites,
            ]
        );
        
        $this->errorCode = 'PREREQUISITE_NOT_MET';
    }
}
