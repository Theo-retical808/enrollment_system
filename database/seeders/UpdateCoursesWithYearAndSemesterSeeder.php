<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class UpdateCoursesWithYearAndSemesterSeeder extends Seeder
{
    public function run(): void
    {
        // Define curriculum structure: course_code => [year_level, semester]
        $curriculum = [
            // Year 1 - 1st Semester
            'CS101' => [1, '1st Semester'],
            'MATH101' => [1, '1st Semester'],
            'ENGL101' => [1, '1st Semester'],
            'PE101' => [1, '1st Semester'],
            'HIST101' => [1, '1st Semester'],
            'ENG101' => [1, '1st Semester'],
            'PHYS101' => [1, '1st Semester'],
            'BUS101' => [1, '1st Semester'],
            'ECON101' => [1, '1st Semester'],
            
            // Year 1 - 2nd Semester
            'CS102' => [1, '2nd Semester'],
            'MATH102' => [1, '2nd Semester'],
            'ENGL102' => [1, '2nd Semester'],
            'PE102' => [1, '2nd Semester'],
            'NSTP101' => [1, '2nd Semester'],
            'CHEM101' => [1, '2nd Semester'],
            'DRAW101' => [1, '2nd Semester'],
            'ACCT101' => [1, '2nd Semester'],
            'STAT101' => [1, '2nd Semester'],
            'BENGL101' => [1, '2nd Semester'],
            
            // Year 2 - 1st Semester
            'CS201' => [2, '1st Semester'],
            'MATH201' => [2, '1st Semester'],
            'CS202' => [2, '1st Semester'],
            'ENGL201' => [2, '1st Semester'],
            'PHYS201' => [2, '1st Semester'],
            'ENG201' => [2, '1st Semester'],
            'CHEM201' => [2, '1st Semester'],
            'BUS201' => [2, '1st Semester'],
            'ECON201' => [2, '1st Semester'],
            
            // Year 2 - 2nd Semester
            'CS203' => [2, '2nd Semester'],
            'MATH202' => [2, '2nd Semester'],
            'CS204' => [2, '2nd Semester'],
            'ENGL202' => [2, '2nd Semester'],
            'PHYS202' => [2, '2nd Semester'],
            'ENG202' => [2, '2nd Semester'],
            'ACCT201' => [2, '2nd Semester'],
            'FIN101' => [2, '2nd Semester'],
            
            // Year 3 - 1st Semester
            'CS301' => [3, '1st Semester'],
            'CS302' => [3, '1st Semester'],
            'CS303' => [3, '1st Semester'],
            'ENG301' => [3, '1st Semester'],
            'BUS301' => [3, '1st Semester'],
            'MGMT201' => [3, '1st Semester'],
            
            // Year 3 - 2nd Semester
            'CS304' => [3, '2nd Semester'],
            'CS305' => [3, '2nd Semester'],
            'CS306' => [3, '2nd Semester'],
            'ENG302' => [3, '2nd Semester'],
            'BUS302' => [3, '2nd Semester'],
            'MKTG201' => [3, '2nd Semester'],
            
            // Year 4 - 1st Semester
            'CS401' => [4, '1st Semester'],
            'CS402' => [4, '1st Semester'],
            'CS403' => [4, '1st Semester'],
            'ENG401' => [4, '1st Semester'],
            'BUS401' => [4, '1st Semester'],
            
            // Year 4 - 2nd Semester
            'CS404' => [4, '2nd Semester'],
            'CS405' => [4, '2nd Semester'],
            'THESIS' => [4, '2nd Semester'],
            'ENG402' => [4, '2nd Semester'],
            'BUS402' => [4, '2nd Semester'],
        ];
        
        foreach ($curriculum as $courseCode => [$yearLevel, $semester]) {
            Course::where('course_code', $courseCode)->update([
                'year_level' => $yearLevel,
                'semester' => $semester,
            ]);
        }
        
        // Update any remaining courses that weren't in the list
        Course::whereNull('year_level')->orWhere('year_level', 0)->update([
            'year_level' => 1,
            'semester' => '1st Semester',
        ]);
        
        $this->command->info('Courses updated with year level and semester information!');
    }
}
