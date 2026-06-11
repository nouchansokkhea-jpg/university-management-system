<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lecturer;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Models\Subject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LecturerController extends Controller
{
    /**
     * Display a listing of the lecturers.
     */
    public function index(Request $request)
    {
        $query = Lecturer::with(['user', 'department', 'subjects']);

        // Search by name, qualification, ID, phone
        if ($request->filled('search')) {
            $search = $request->input('search');
            $like = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('lecturer_id', $like, "%{$search}%")
                  ->orWhere('qualification', $like, "%{$search}%")
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

        $lecturers = $query->paginate(10);
        $departments = Department::all();
        $faculties = \App\Models\Faculty::all();
        $courses = \App\Models\Course::all();
        $allLecturers = Lecturer::with('user')->get();

        return view('lecturers.index', compact('lecturers', 'departments', 'faculties', 'courses', 'allLecturers'));
    }

    /**
     * Show the form for creating a new lecturer.
     */
    public function create()
    {
        $departments = Department::all();
        return view('lecturers.create', compact('departments'));
    }

    /**
     * Store a newly created lecturer in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'qualification' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'salary' => ['required', 'numeric', 'min:0'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'retired', 'inactive'])],
        ]);

        // 1. Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign lecturer role
        $lecturerRole = Role::where('slug', 'lecturer')->first();
        if ($lecturerRole) {
            $user->roles()->attach($lecturerRole->id);
        }

        // 2. Generate Lecturer ID (LEC-XXXX)
        $count = Lecturer::count() + 1001;
        $lecturerId = 'LEC-' . $count;

        // 3. Create Lecturer profile
        Lecturer::create([
            'user_id' => $user->id,
            'lecturer_id' => $lecturerId,
            'qualification' => $request->qualification,
            'department_id' => $request->department_id,
            'salary' => $request->salary,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('lecturers.index')->with('success', 'Lecturer registered successfully with ID: ' . $lecturerId);
    }

    /**
     * Display the specified lecturer.
     */
    public function show(Lecturer $lecturer)
    {
        $lecturer->load(['user', 'department.faculty', 'subjects.course', 'exams.subject', 'materials.subject']);
        return view('lecturers.show', compact('lecturer'));
    }

    /**
     * Show the form for editing the specified lecturer.
     */
    public function edit(Lecturer $lecturer)
    {
        $lecturer->load('user');
        $departments = Department::all();
        return view('lecturers.edit', compact('lecturer', 'departments'));
    }

    /**
     * Update the specified lecturer in storage.
     */
    public function update(Request $request, Lecturer $lecturer)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($lecturer->user_id)],
            'password' => ['nullable', 'string', 'min:8'],
            'qualification' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'salary' => ['required', 'numeric', 'min:0'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'retired', 'inactive'])],
        ]);

        // Update user details
        $user = $lecturer->user;
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Update profile
        $lecturer->update([
            'qualification' => $request->qualification,
            'department_id' => $request->department_id,
            'salary' => $request->salary,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        return redirect()->route('lecturers.show', $lecturer->id)->with('success', 'Lecturer details updated successfully.');
    }

    /**
     * Remove the specified lecturer from storage.
     */
    public function destroy(Lecturer $lecturer)
    {
        $lecturer->user->delete();
        return redirect()->route('lecturers.index')->with('success', 'Lecturer record deleted successfully.');
    }

    /**
     * Show view to assign subjects to the lecturer.
     */
    public function showAssignSubjects(Lecturer $lecturer)
    {
        $lecturer->load(['user', 'subjects']);
        $subjects = Subject::with(['course', 'lecturer.user'])->get();

        return view('lecturers.assign-subjects', compact('lecturer', 'subjects'));
    }

    /**
     * Process subject assignment.
     */
    public function assignSubjects(Request $request, Lecturer $lecturer)
    {
        $request->validate([
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['exists:subjects,id'],
        ]);

        $subjectIds = $request->input('subject_ids', []);

        // Remove this lecturer from their old subjects
        Subject::where('lecturer_id', $lecturer->id)->update(['lecturer_id' => null]);

        // Assign this lecturer to the selected subjects
        if (count($subjectIds) > 0) {
            Subject::whereIn('id', $subjectIds)->update(['lecturer_id' => $lecturer->id]);
        }

        return redirect()->route('lecturers.show', $lecturer->id)->with('success', 'Subjects assigned successfully.');
    }

    /**
     * Store a new department.
     */
    public function storeDepartment(Request $request)
    {
        $request->validate([
            'faculty_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:departments,code'],
            'description' => ['nullable', 'string'],
        ]);

        // Find or create Faculty by name
        $facultyName = $request->faculty_name;
        $facultyCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $facultyName), 0, 4));
        $baseCode = $facultyCode ?: 'FAC';
        $code = $baseCode;
        $i = 1;
        while (\App\Models\Faculty::where('code', $code)->exists()) {
            $code = $baseCode . $i++;
        }

        $faculty = \App\Models\Faculty::firstOrCreate(
            ['name' => $facultyName],
            ['code' => $code, 'description' => 'Auto-created during department registration']
        );

        Department::create([
            'faculty_id' => $faculty->id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Department added successfully.');
    }

    /**
     * Store a new subject.
     */
    public function storeSubject(Request $request)
    {
        $request->validate([
            'course_name' => ['required', 'string', 'max:255'],
            'subject_code' => ['required', 'string', 'max:50', 'unique:subjects,subject_code'],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:1', 'max:10'],
            'semester' => ['required', 'integer', 'min:1', 'max:8'],
            'department_id' => ['required', 'exists:departments,id'],
            'lecturer_id' => ['nullable', 'exists:lecturers,id'],
        ]);

        // Find or create Course by name under the selected department
        $courseName = $request->course_name;
        $courseCode = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $courseName), 0, 6));
        $baseCode = $courseCode ?: 'CRS';
        $code = $baseCode;
        $i = 1;
        while (\App\Models\Course::where('course_code', $code)->exists()) {
            $code = $baseCode . $i++;
        }

        $course = \App\Models\Course::firstOrCreate(
            [
                'name' => $courseName,
                'department_id' => $request->department_id,
            ],
            [
                'course_code' => $code,
                'description' => 'Auto-created during subject registration',
                'duration_years' => 4,
            ]
        );

        Subject::create([
            'course_id' => $course->id,
            'subject_code' => $request->subject_code,
            'name' => $request->name,
            'credits' => $request->credits,
            'semester' => $request->semester,
            'department_id' => $request->department_id,
            'lecturer_id' => $request->lecturer_id,
        ]);

        return redirect()->back()->with('success', 'Subject added successfully.');
    }
}
