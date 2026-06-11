<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:super_admin,registrar,lecturer,student,staff'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Process role selection if provided
        $roleSlug = $request->input('role');
        if ($roleSlug) {
            $role = \App\Models\Role::where('slug', $roleSlug)->first();
            if ($role) {
                $user->roles()->attach($role->id);

                // Try to find or create a default department and faculty for profiles
                $dept = \App\Models\Department::first();
                if (!$dept) {
                    $faculty = \App\Models\Faculty::first();
                    if (!$faculty) {
                        $faculty = \App\Models\Faculty::create([
                            'name' => 'Faculty of Engineering & Technology',
                            'code' => 'ENG',
                            'description' => 'Engineering and Applied Sciences'
                        ]);
                    }
                    $dept = \App\Models\Department::create([
                        'faculty_id' => $faculty->id,
                        'name' => 'Computer Science & Engineering',
                        'code' => 'CSE',
                        'description' => 'Computing disciplines'
                    ]);
                }

                // Automatically configure profile fields based on the chosen role
                if ($roleSlug === 'student') {
                    $year = date('Y');
                    $count = \App\Models\Student::count() + 1;
                    $studentId = 'STU-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                    $student = \App\Models\Student::create([
                        'user_id' => $user->id,
                        'student_id' => $studentId,
                        'gender' => 'other',
                        'dob' => '2005-01-01',
                        'phone' => '+15550000',
                        'address' => 'University Campus Dorms',
                        'department_id' => $dept->id,
                        'enrollment_date' => now()->toDateString(),
                        'status' => 'active',
                        'academic_history' => ['high_school' => 'N/A', 'gpa' => '0.00'],
                    ]);

                    // Auto-enroll self-registered student in all subjects of their department
                    $academicYear = \App\Models\AcademicYear::where('is_active', true)->first() 
                        ?: \App\Models\AcademicYear::first();
                        
                    if ($academicYear) {
                        $subjects = \App\Models\Subject::where('department_id', $dept->id)->get();
                        foreach ($subjects as $subject) {
                            \App\Models\Enrollment::create([
                                'student_id' => $student->id,
                                'subject_id' => $subject->id,
                                'academic_year_id' => $academicYear->id,
                                'semester' => $subject->semester,
                                'status' => 'approved',
                            ]);
                        }
                    }
                } elseif ($roleSlug === 'lecturer') {
                    $count = \App\Models\Lecturer::count() + 1;
                    $lecturerId = 'LEC-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                    \App\Models\Lecturer::create([
                        'user_id' => $user->id,
                        'lecturer_id' => $lecturerId,
                        'qualification' => 'Master of Science',
                        'department_id' => $dept->id,
                        'salary' => 5000.00,
                        'phone' => '+15550000',
                        'status' => 'active',
                    ]);
                } elseif ($roleSlug === 'staff') {
                    $count = \App\Models\Staff::count() + 1;
                    $staffId = 'STF-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                    \App\Models\Staff::create([
                        'user_id' => $user->id,
                        'staff_id' => $staffId,
                        'department_id' => $dept->id,
                        'designation' => 'Administrative Assistant',
                        'phone' => '+15550000',
                        'salary' => 3000.00,
                        'status' => 'active',
                    ]);
                }
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
