<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'department']);

        // Search by name, email, or student ID
        if ($request->filled('search')) {
            $search = $request->input('search');
            $like = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('student_id', $like, "%{$search}%")
                  ->orWhere('phone', $like, "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search, $like) {
                      $uq->where('name', $like, "%{$search}%")
                         ->orWhere('email', $like, "%{$search}%");
                  });
            });
        }

        // Filter by Department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $students = $query->paginate(10);
        $departments = Department::all();

        return view('students.index', compact('students', 'departments'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        $departments = Department::all();
        return view('students.create', compact('departments'));
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'dob' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'department_id' => ['required', 'exists:departments,id'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'graduated', 'inactive'])],
            'photo' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        // 1. Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign student role
        $studentRole = Role::where('slug', 'student')->first();
        if ($studentRole) {
            $user->roles()->attach($studentRole->id);
        }

        // 2. Generate Student ID (STU-YYYY-XXXX)
        $year = date('Y', strtotime($request->enrollment_date));
        $count = Student::whereYear('enrollment_date', $year)->count() + 1;
        $studentId = 'STU-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        // 3. Handle Photo Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('student-photos', 'public');
        }

        // 4. Create Student Profile
        $student = Student::create([
            'user_id' => $user->id,
            'student_id' => $studentId,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'address' => $request->address,
            'department_id' => $request->department_id,
            'enrollment_date' => $request->enrollment_date,
            'status' => $request->status,
            'photo_path' => $photoPath,
            'academic_history' => [
                'high_school' => $request->input('high_school', 'N/A'),
                'gpa' => $request->input('high_school_gpa', '0.00'),
            ],
        ]);

        // 5. Auto-enroll student in all subjects of their department for the active academic year
        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first() 
            ?: \App\Models\AcademicYear::first();
            
        if ($academicYear) {
            $subjects = \App\Models\Subject::where('department_id', $request->department_id)->get();
            foreach ($subjects as $subject) {
                \App\Models\Enrollment::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'academic_year_id' => $academicYear->id,
                    'semester' => $subject->semester,
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('students.index')->with('success', 'Student registered successfully with ID: ' . $studentId);
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        $student->load(['user', 'department.faculty', 'enrollments.subject.course', 'attendance.subject', 'grades.subject', 'fees.payments']);

        // Calculate average attendance
        $totalAtt = $student->attendance()->count();
        $presAtt = $student->attendance()->where('status', 'present')->count();
        $attendanceRate = $totalAtt > 0 ? round(($presAtt / $totalAtt) * 100, 1) : 100;

        // Calculate overall GPA (only overall subject grades)
        $grades = $student->grades()->whereNull('exam_id')->get();
        $gpa = $grades->count() > 0 ? round($grades->avg('gpa_value'), 2) : 0.00;

        // Financial balances
        $feesOutstanding = 0;
        foreach ($student->fees as $fee) {
            $net = $fee->total_amount - $fee->scholarship_amount - $fee->discount_amount;
            $paid = $fee->payments->sum('amount');
            $feesOutstanding += max(0, $net - $paid);
        }

        return view('students.show', compact('student', 'attendanceRate', 'gpa', 'feesOutstanding'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        $student->load('user');
        $departments = Department::all();
        return view('students.edit', compact('student', 'departments'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($student->user_id)],
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'dob' => ['required', 'date'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
            'department_id' => ['required', 'exists:departments,id'],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'graduated', 'inactive'])],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        // 1. Update User
        $user = $student->user;
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // 2. Handle Photo Upload
        $photoPath = $student->photo_path;
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('student-photos', 'public');
        }

        $oldDepartmentId = $student->department_id;

        // 3. Update Profile
        $student->update([
            'gender' => $request->gender,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'address' => $request->address,
            'department_id' => $request->department_id,
            'status' => $request->status,
            'photo_path' => $photoPath,
            'academic_history' => [
                'high_school' => $request->input('high_school', $student->academic_history['high_school'] ?? 'N/A'),
                'gpa' => $request->input('high_school_gpa', $student->academic_history['gpa'] ?? '0.00'),
            ],
        ]);

        // If department changed, auto-enroll in new department's subjects
        if ($oldDepartmentId != $request->department_id) {
            $academicYear = \App\Models\AcademicYear::where('is_active', true)->first() 
                ?: \App\Models\AcademicYear::first();
                
            if ($academicYear) {
                $subjects = \App\Models\Subject::where('department_id', $request->department_id)->get();
                foreach ($subjects as $subject) {
                    \App\Models\Enrollment::firstOrCreate([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'academic_year_id' => $academicYear->id,
                        'semester' => $subject->semester,
                    ], [
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                    ]);
                }
            }
        }

        return redirect()->route('students.show', $student->id)->with('success', 'Student details updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        // Delete user (cascade deletes profile and related data due to foreign keys)
        $student->user->delete();

        return redirect()->route('students.index')->with('success', 'Student record deleted successfully.');
    }
}
