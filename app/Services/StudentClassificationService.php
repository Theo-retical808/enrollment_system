<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Enrollment;

class StudentClassificationService
{
    /**
     * Determine if a student is regular or irregular.
     */
    public function classifyStudent(Student $student): string
    {
        return $student->isRegular() ? 'regular' : 'irregular';
    }

    /**
     * Check if a student has failed courses.
     */
    public function hasFailedCourses(Student $student): bool
    {
        return $student->hasFailedCourses();
    }

    /**
     * Get the appropriate enrollment workflow for a student.
     */
    public function getEnrollmentWorkflow(Student $student): string
    {
        if ($this->classifyStudent($student) === 'regular') {
            return 'automatic_schedule_assignment';
        }
        
        return 'manual_course_selection';
    }

    /**
     * Route student to appropriate enrollment process.
     */
    public function routeToEnrollment(Student $student): array
    {
        $classification = $this->classifyStudent($student);
        $workflow = $this->getEnrollmentWorkflow($student);
        
        return [
            'classification' => $classification,
            'workflow' => $workflow,
            'route' => $classification === 'regular' 
                ? 'enrollment.regular' 
                : 'enrollment.irregular',
            'message' => $classification === 'regular'
                ? 'You will be automatically assigned a schedule based on your program.'
                : 'You can select your own courses. Please ensure prerequisites are met.',
        ];
    }

    /**
     * Get failed courses for irregular students.
     */
    public function getFailedCourses(Student $student): \Illuminate\Database\Eloquent\Collection
    {
        return $student->completedCourses()
            ->wherePivot('passed', false)
            ->get();
    }

    /**
     * Check if student needs to retake specific courses.
     */
    public function needsRetake(Student $student): bool
    {
        return $this->getFailedCourses($student)->isNotEmpty();
    }

    /**
     * Get recommended action for student based on classification.
     */
    public function getRecommendedAction(Student $student): array
    {
        $classification = $this->classifyStudent($student);
        
        if ($classification === 'regular') {
            return [
                'action' => 'proceed_to_automatic_enrollment',
                'description' => 'Proceed with automatic schedule assignment',
                'next_step' => 'Review and submit your assigned schedule',
            ];
        }
        
        $failedCourses = $this->getFailedCourses($student);
        
        return [
            'action' => 'manual_course_selection',
            'description' => 'Select courses manually with guidance',
            'next_step' => 'Browse available courses and build your schedule',
            'failed_courses_count' => $failedCourses->count(),
            'needs_retake' => $failedCourses->isNotEmpty(),
            'retake_courses' => $failedCourses->pluck('course_code')->toArray(),
        ];
    }
}