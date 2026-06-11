<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Exam;
use App\Models\AcademicYear;

class GradeController extends Controller
{
    /**
     * Show grade entry form for a subject.
     */
    public function index(Request $request)
    {
        $subjects = Subject::with('course')->get();
        $selectedSubject = null;
        $exams = [];
        $students = [];
        
        if ($request->filled('subject_id')) {
            $selectedSubject = Subject::findOrFail($request->subject_id);
            $exams = Exam::where('subject_id', $selectedSubject->id)->get();

            // Self-healing: auto-enroll any active students of the department into this subject if not already enrolled
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first() 
                ?: \App\Models\AcademicYear::first();
            
            if ($academicYear) {
                $activeStudents = Student::where('department_id', $selectedSubject->department_id)
                    ->where('status', 'active')
                    ->get();
                
                foreach ($activeStudents as $activeStudent) {
                    Enrollment::firstOrCreate([
                        'student_id' => $activeStudent->id,
                        'subject_id' => $selectedSubject->id,
                        'academic_year_id' => $academicYear->id,
                        'semester' => $selectedSubject->semester,
                    ], [
                        'status' => 'approved',
                    ]);
                }
            }

            // Get enrolled students
            $studentIds = Enrollment::where('subject_id', $selectedSubject->id)
                ->where('status', 'approved')
                ->pluck('student_id');
            
            $students = Student::whereIn('id', $studentIds)->with('user')->get();

            // Fetch existing grades for this subject and exam
            $examId = $request->input('exam_id'); // can be null for overall course grade
            $existingGrades = Grade::where('subject_id', $selectedSubject->id)
                ->where('exam_id', $examId ?: null)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $student->grade_entry = $existingGrades->get($student->id);
            }
        }

        return view('grades.index', compact('subjects', 'selectedSubject', 'exams', 'students'));
    }

    /**
     * Store input grades.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'marks' => ['required', 'array'],
            'marks.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $subjectId = $request->subject_id;
        $examId = $request->exam_id ?: null;
        $marksData = $request->marks;
        $subject = Subject::findOrFail($subjectId);
        $academicYear = AcademicYear::where('is_active', true)->first() ?: AcademicYear::first();
        
        foreach ($marksData as $studentId => $marks) {
            if ($marks === null || $marks === '') {
                continue;
            }

            // Calculate letter grade and GPA point
            list($letter, $gpaVal) = $this->calculateGradeAndGpa($marks);

            Grade::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'exam_id' => $examId,
                    'semester' => $subject->semester,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'marks_obtained' => $marks,
                    'grade_letter' => $letter,
                    'gpa_value' => $gpaVal,
                ]
            );
        }

        return redirect()->route('grades.index', ['subject_id' => $subjectId, 'exam_id' => $examId])
            ->with('success', 'Grades recorded and scaled successfully.');
    }

    /**
     * View Academic Transcript for a student.
     */
    public function transcript(Student $student)
    {
        $student->load(['user', 'department']);
        
        // Fetch overall subject grades (where exam_id is null) grouped by semester
        $grades = Grade::where('student_id', $student->id)
            ->whereNull('exam_id')
            ->with(['subject', 'academicYear'])
            ->get()
            ->groupBy('semester');

        // Calculate CGPA
        $allOverallGrades = Grade::where('student_id', $student->id)->whereNull('exam_id')->get();
        
        $totalCredits = 0;
        $weightedGpaPoints = 0.00;

        foreach ($allOverallGrades as $grade) {
            $credits = $grade->subject->credits;
            $totalCredits += $credits;
            $weightedGpaPoints += $grade->gpa_value * $credits;
        }

        $cgpa = $totalCredits > 0 ? round($weightedGpaPoints / $totalCredits, 2) : 0.00;

        return view('grades.transcript', compact('student', 'grades', 'cgpa', 'totalCredits'));
    }

    /**
     * Grade scale calculator.
     */
    private function calculateGradeAndGpa($marks)
    {
        if ($marks >= 85) return ['A', 4.00];
        if ($marks >= 75) return ['B+', 3.50];
        if ($marks >= 70) return ['B', 3.00];
        if ($marks >= 65) return ['C+', 2.50];
        if ($marks >= 60) return ['C', 2.00];
        if ($marks >= 50) return ['D', 1.00];
        return ['F', 0.00];
    }
}
