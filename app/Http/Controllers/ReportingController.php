<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportingController extends Controller
{
    /**
     * View reports home.
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Stream CSV export of report types.
     */
    public function export(Request $request, string $type)
    {
        switch ($type) {
            case 'students':
                return $this->exportStudents($request);
            case 'attendance':
                return $this->exportAttendance();
            case 'finance':
                return $this->exportFinance();
            case 'lecturers':
                return $this->exportLecturers();
            case 'performance':
                return $this->exportPerformance();
            default:
                abort(404, 'Report type not found.');
        }
    }

    private function exportStudents(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_report_' . date('Ymd') . '.csv"',
        ];

        // Retrieve filters
        $departmentId = $request->input('department_id');
        $status = $request->input('status');

        // Retrieve columns to export. Default to standard columns if not provided.
        $selectedColumns = $request->input('columns', [
            'student_id', 'name', 'email', 'gender', 'phone', 'department', 'enrollment_date', 'status'
        ]);

        $callback = function () use ($departmentId, $status, $selectedColumns) {
            $file = fopen('php://output', 'w');

            // Build CSV Headers based on selected columns
            $csvHeaders = [];
            foreach ($selectedColumns as $col) {
                switch ($col) {
                    case 'student_id': $csvHeaders[] = 'Student ID'; break;
                    case 'name': $csvHeaders[] = 'Full Name'; break;
                    case 'email': $csvHeaders[] = 'Email'; break;
                    case 'gender': $csvHeaders[] = 'Gender'; break;
                    case 'phone': $csvHeaders[] = 'Phone'; break;
                    case 'department': $csvHeaders[] = 'Department'; break;
                    case 'enrollment_date': $csvHeaders[] = 'Enrollment Date'; break;
                    case 'status': $csvHeaders[] = 'Status'; break;
                    case 'high_school': $csvHeaders[] = 'High School'; break;
                    case 'high_school_gpa': $csvHeaders[] = 'High School GPA'; break;
                }
            }
            fputcsv($file, $csvHeaders);

            // Build student query with filters
            $query = Student::with(['user', 'department']);
            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }
            if ($status) {
                $query->where('status', $status);
            }

            $students = $query->get();
            foreach ($students as $student) {
                $row = [];
                foreach ($selectedColumns as $col) {
                    switch ($col) {
                        case 'student_id': $row[] = $student->student_id; break;
                        case 'name': $row[] = $student->user->name; break;
                        case 'email': $row[] = $student->user->email; break;
                        case 'gender': $row[] = ucfirst($student->gender); break;
                        case 'phone': $row[] = $student->phone; break;
                        case 'department': $row[] = $student->department ? $student->department->name : 'N/A'; break;
                        case 'enrollment_date': $row[] = $student->enrollment_date ? $student->enrollment_date->format('Y-m-d') : 'N/A'; break;
                        case 'status': $row[] = ucfirst($student->status); break;
                        case 'high_school': $row[] = $student->academic_history['high_school'] ?? 'N/A'; break;
                        case 'high_school_gpa': $row[] = $student->academic_history['gpa'] ?? '0.00'; break;
                    }
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function exportAttendance()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance_report_' . date('Ymd') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Subject Code', 'Subject Name', 'Student ID', 'Student Name', 'Present Count', 'Total Count', 'Attendance Rate (%)']);

            $subjects = Subject::with('enrollments.student.user')->get();
            foreach ($subjects as $subject) {
                foreach ($subject->enrollments as $enrollment) {
                    $student = $enrollment->student;
                    if (!$student) continue;

                    $total = Attendance::where('student_id', $student->id)->where('subject_id', $subject->id)->count();
                    $present = Attendance::where('student_id', $student->id)->where('subject_id', $subject->id)->where('status', 'present')->count();
                    $rate = $total > 0 ? round(($present / $total) * 100, 1) : 100.0;

                    fputcsv($file, [
                        $subject->subject_code,
                        $subject->name,
                        $student->student_id,
                        $student->user->name,
                        $present,
                        $total,
                        $rate . '%'
                    ]);
                }
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function exportFinance()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="financial_report_' . date('Ymd') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student ID', 'Student Name', 'Semester', 'Billed Amount ($)', 'Scholarship ($)', 'Discount ($)', 'Paid ($)', 'Outstanding ($)', 'Status']);

            $fees = Fee::with(['student.user', 'payments'])->get();
            foreach ($fees as $fee) {
                $net = $fee->total_amount - $fee->scholarship_amount - $fee->discount_amount;
                $paid = $fee->payments->sum('amount');
                $outstanding = max(0, $net - $paid);

                fputcsv($file, [
                    $fee->student->student_id,
                    $fee->student->user->name,
                    'Semester ' . $fee->semester,
                    number_format($fee->total_amount, 2),
                    number_format($fee->scholarship_amount, 2),
                    number_format($fee->discount_amount, 2),
                    number_format($paid, 2),
                    number_format($outstanding, 2),
                    ucfirst($fee->status)
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function exportLecturers()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="lecturer_report_' . date('Ymd') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Lecturer ID', 'Lecturer Name', 'Email', 'Department', 'Phone', 'Qualification', 'Monthly Salary ($)', 'Status']);

            $lecturers = Lecturer::with(['user', 'department'])->get();
            foreach ($lecturers as $lecturer) {
                fputcsv($file, [
                    $lecturer->lecturer_id,
                    $lecturer->user->name,
                    $lecturer->user->email,
                    $lecturer->department->name,
                    $lecturer->phone,
                    $lecturer->qualification,
                    number_format($lecturer->salary, 2),
                    ucfirst($lecturer->status)
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function exportPerformance()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="academic_performance_' . date('Ymd') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student ID', 'Student Name', 'Subject Code', 'Subject Name', 'Marks Obtained', 'Letter Grade', 'GPA Value']);

            $grades = Grade::with(['student.user', 'subject'])->whereNull('exam_id')->get();
            foreach ($grades as $grade) {
                fputcsv($file, [
                    $grade->student->student_id,
                    $grade->student->user->name,
                    $grade->subject->subject_code,
                    $grade->subject->name,
                    $grade->marks_obtained ?? 'N/A',
                    $grade->grade_letter,
                    number_format($grade->gpa_value, 2)
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
