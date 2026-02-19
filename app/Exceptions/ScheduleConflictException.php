<?php

namespace App\Exceptions;

class ScheduleConflictException extends ValidationException
{
    public function __construct(string $newCourse, array $conflictingCourses)
    {
        $conflictList = array_map(function ($conflict) {
            return "{$conflict['course_code']} ({$conflict['schedule_day']} {$conflict['start_time']}-{$conflict['end_time']})";
        }, $conflictingCourses);
        
        $message = "Schedule conflict detected for {$newCourse}. Conflicts with: " . 
                   implode(', ', $conflictList);
        
        parent::__construct(
            $message,
            $conflictingCourses,
            [
                'new_course' => $newCourse,
                'conflicting_courses' => $conflictingCourses,
            ]
        );
        
        $this->errorCode = 'SCHEDULE_CONFLICT';
    }
}
