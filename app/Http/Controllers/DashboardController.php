<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Staff;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\BookBorrow;
use App\Models\RoomAllocation;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use App\Models\Event;
use App\Models\Exam;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the main UMS dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Super Admin or Registrar Dashboard
        if ($user->hasRole(['super_admin', 'registrar'])) {
            $stats = [
                'total_students' => Student::count(),
                'total_lecturers' => Lecturer::count(),
                'total_staff' => Staff::count(),
                'total_courses' => Course::count(),
                'total_subjects' => Subject::count(),
            ];

            // Attendance rate
            $totalAttendance = Attendance::count();
            $presentCount = Attendance::where('status', 'present')->count();
            $stats['attendance_rate'] = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 100;

            // Financial summary
            $totalFees = Fee::sum('total_amount') - Fee::sum('scholarship_amount') - Fee::sum('discount_amount');
            $totalPaid = Payment::sum('amount');
            $stats['fees_billed'] = $totalFees;
            $stats['fees_collected'] = $totalPaid;
            $stats['fees_outstanding'] = max(0, $totalFees - $totalPaid);

            // Chart data: Enrollments by department
            $enrollmentsByDept = DB::table('students')
                ->join('departments', 'students.department_id', '=', 'departments.id')
                ->select('departments.code', DB::raw('count(students.id) as count'))
                ->groupBy('departments.code')
                ->get();

            // Chart data: Monthly collections
            $driver = DB::connection()->getDriverName();
            if ($driver === 'sqlite') {
                $monthExpr = "strftime('%m', payment_date)";
            } elseif ($driver === 'pgsql') {
                $monthExpr = "to_char(payment_date, 'MM')";
            } else {
                $monthExpr = "DATE_FORMAT(payment_date, '%m')";
            }

            $monthlyPayments = DB::table('payments')
                ->select(DB::raw("$monthExpr as month"), DB::raw('sum(amount) as total'))
                ->groupBy(DB::raw($monthExpr))
                ->orderBy(DB::raw($monthExpr))
                ->get();

            // Upcoming events
            $events = Event::where('end_date', '>=', now())
                ->orderBy('start_date')
                ->take(5)
                ->get();

            return view('dashboard.admin', compact('stats', 'enrollmentsByDept', 'monthlyPayments', 'events'));
        }

        // 2. Lecturer Dashboard
        if ($user->hasRole('lecturer')) {
            $lecturer = $user->lecturer;

            if (!$lecturer) {
                return view('dashboard.pending', ['message' => 'Your Lecturer profile is being configured by the academic department. Please contact the registrar.']);
            }

            $subjects = Subject::where('lecturer_id', $lecturer->id)->with('course')->get();
            $subjectIds = $subjects->pluck('id');

            $stats = [
                'assigned_subjects_count' => $subjects->count(),
                'total_students' => Enrollment::whereIn('subject_id', $subjectIds)->distinct('student_id')->count(),
                'total_materials' => $lecturer->materials()->count(),
                'total_exams' => Exam::whereIn('subject_id', $subjectIds)->count(),
            ];

            // Lecturer attendance tracking
            $totalAttendance = Attendance::whereIn('subject_id', $subjectIds)->count();
            $presentCount = Attendance::whereIn('subject_id', $subjectIds)->where('status', 'present')->count();
            $stats['attendance_rate'] = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 100;

            $upcomingExams = Exam::whereIn('subject_id', $subjectIds)
                ->where('exam_date', '>=', now()->toDateString())
                ->with('subject')
                ->get();

            $pendingLeaves = LeaveRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->get();

            return view('dashboard.lecturer', compact('stats', 'subjects', 'upcomingExams', 'pendingLeaves'));
        }

        // 3. Student Dashboard
        if ($user->hasRole('student')) {
            $student = $user->student;

            if (!$student) {
                return view('dashboard.pending', ['message' => 'Your Student profile registration is pending registrar approval. Please check back shortly.']);
            }

            // Self-healing: Auto-enroll student in all subjects of their department if not already enrolled
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first() 
                ?: \App\Models\AcademicYear::first();
                
            if ($academicYear) {
                $subjects = \App\Models\Subject::where('department_id', $student->department_id)->get();
                foreach ($subjects as $subject) {
                    Enrollment::firstOrCreate([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'academic_year_id' => $academicYear->id,
                        'semester' => $subject->semester,
                    ], [
                        'status' => 'approved',
                    ]);
                }
            }

            $enrollments = Enrollment::where('student_id', $student->id)
                ->with(['subject.lecturer.user', 'subject.course'])
                ->get();
            
            $subjectIds = $enrollments->pluck('subject_id');

            // Grade list and GPA calculation
            $grades = Grade::where('student_id', $student->id)->whereNull('exam_id')->get();
            $gpa = $grades->count() > 0 ? round($grades->avg('gpa_value'), 2) : 0.00;

            // Attendance rate
            $totalAtt = Attendance::where('student_id', $student->id)->count();
            $presAtt = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
            $attRate = $totalAtt > 0 ? round(($presAtt / $totalAtt) * 100, 1) : 100;

            // Fees
            $fees = Fee::where('student_id', $student->id)->with('payments')->get();
            $totalDue = 0;
            $totalPaid = 0;
            foreach ($fees as $fee) {
                $net = $fee->total_amount - $fee->scholarship_amount - $fee->discount_amount;
                $paid = $fee->payments->sum('amount');
                $totalDue += max(0, $net - $paid);
                $totalPaid += $paid;
            }

            // Hostel Room allocation
            $allocation = RoomAllocation::where('student_id', $student->id)
                ->where('status', 'active')
                ->with('room.hostel')
                ->first();

            // Library books borrowed
            $borrows = BookBorrow::where('user_id', $user->id)
                ->where('status', 'borrowed')
                ->with('book')
                ->get();

            $events = Event::where('audience', 'all')
                ->orWhere('audience', 'students')
                ->where('end_date', '>=', now())
                ->take(3)
                ->get();

            return view('dashboard.student', compact('student', 'enrollments', 'gpa', 'attRate', 'totalDue', 'totalPaid', 'allocation', 'borrows', 'events'));
        }

        // 4. Staff Dashboard
        if ($user->hasRole('staff')) {
            $staff = $user->staff;

            if (!$staff) {
                return view('dashboard.pending', ['message' => 'Your Administrative Staff profile is currently being processed.']);
            }

            $payrolls = Payroll::where('user_id', $user->id)->orderBy('year', 'desc')->orderBy('month', 'desc')->take(6)->get();
            $leaves = LeaveRequest::where('user_id', $user->id)->orderBy('created_at', 'desc')->take(5)->get();

            $stats = [
                'salary' => $staff->salary,
                'designation' => $staff->designation,
                'department' => $staff->department ? $staff->department->name : 'General',
                'pending_leaves_count' => LeaveRequest::where('user_id', $user->id)->where('status', 'pending')->count(),
            ];

            return view('dashboard.staff', compact('stats', 'payrolls', 'leaves'));
        }

        return view('dashboard.pending', ['message' => 'Welcome to Apex UMS! Your account has been created, but your institutional role is pending assignment by the System Administrator.']);
    }
}
