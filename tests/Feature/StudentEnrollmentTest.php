<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Role;
use App\Models\Subject;
use App\Models\User;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the student role
        Role::create([
            'name' => 'Student',
            'slug' => 'student',
            'description' => 'Student role'
        ]);

        // Create super admin role for admin testing
        Role::create([
            'name' => 'Super Admin',
            'slug' => 'super_admin',
            'description' => 'Admin role'
        ]);
    }

    public function test_student_auto_enrolled_on_admin_registration(): void
    {
        // 1. Setup Academic structures
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);
        $academicYear = AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
        $course = Course::create(['department_id' => $department->id, 'course_code' => 'CS-BSC', 'name' => 'B.Sc. CS']);
        
        // Create 2 subjects for this department
        $subject1 = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'CS-101',
            'name' => 'Intro to Programming',
            'credits' => 3,
            'semester' => 1,
            'department_id' => $department->id,
        ]);
        $subject2 = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'CS-102',
            'name' => 'Data Structures',
            'credits' => 4,
            'semester' => 1,
            'department_id' => $department->id,
        ]);

        // 2. Authenticate as Admin
        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        $response = $this->actingAs($admin)->post('/students', [
            'name' => 'John Doe',
            'email' => 'johndoe@test.com',
            'password' => 'password123',
            'gender' => 'male',
            'dob' => '2005-05-15',
            'phone' => '+123456789',
            'address' => '123 Main St',
            'department_id' => $department->id,
            'enrollment_date' => '2025-09-01',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('students.index'));

        // Verify Student profile exists
        $student = Student::whereHas('user', function ($q) {
            $q->where('email', 'johndoe@test.com');
        })->first();

        $this->assertNotNull($student);

        // Verify enrollments were created with status 'approved'
        $enrollments = Enrollment::where('student_id', $student->id)->get();
        $this->assertCount(2, $enrollments);
        
        $this->assertTrue($enrollments->contains('subject_id', $subject1->id));
        $this->assertTrue($enrollments->contains('subject_id', $subject2->id));
        $this->assertEquals('approved', $enrollments->first()->status);
    }

    public function test_student_auto_enrolled_on_self_registration(): void
    {
        // 1. Setup Academic structures
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);
        $academicYear = AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);
        $course = Course::create(['department_id' => $department->id, 'course_code' => 'CS-BSC', 'name' => 'B.Sc. CS']);
        
        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'CS-101',
            'name' => 'Intro to Programming',
            'credits' => 3,
            'semester' => 1,
            'department_id' => $department->id,
        ]);

        // 2. Perform Self-Registration
        $response = $this->post('/register', [
            'name' => 'Jane Student',
            'email' => 'jane@student.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'student',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));

        $student = Student::whereHas('user', function ($q) {
            $q->where('email', 'jane@student.com');
        })->first();

        $this->assertNotNull($student);

        // Verify enrollments were created with status 'approved'
        $enrollments = Enrollment::where('student_id', $student->id)->get();
        $this->assertCount(1, $enrollments);
        $this->assertEquals($subject->id, $enrollments->first()->subject_id);
        $this->assertEquals('approved', $enrollments->first()->status);
    }

    public function test_student_search_is_case_insensitive(): void
    {
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);

        // Create student with name "Nou Chansokhea"
        $user = User::create([
            'name' => 'Nou Chansokhea',
            'email' => 'nou@test.com',
            'password' => bcrypt('password'),
        ]);
        Student::create([
            'user_id' => $user->id,
            'student_id' => 'STU-2023-0001',
            'gender' => 'male',
            'dob' => '2004-04-14',
            'phone' => '+855715734762',
            'address' => 'Phnom Penh',
            'department_id' => $department->id,
            'enrollment_date' => '2023-12-06',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        // Search with lowercase "nou chansokhea"
        $response = $this->actingAs($admin)->get('/students?search=nou chansokhea');
        $response->assertStatus(200);
        $response->assertSee('Nou Chansokhea');
        $response->assertSee('STU-2023-0001');
    }

    public function test_lecturer_search_is_case_insensitive(): void
    {
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);

        // Create lecturer with name "Charles Xavier"
        $user = User::create([
            'name' => 'Charles Xavier',
            'email' => 'charles@test.com',
            'password' => bcrypt('password'),
        ]);
        \App\Models\Lecturer::create([
            'user_id' => $user->id,
            'lecturer_id' => 'LEC-1001',
            'qualification' => 'Ph.D.',
            'department_id' => $department->id,
            'salary' => 5000,
            'phone' => '+123456789',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        // Search with lowercase "charles"
        $response = $this->actingAs($admin)->get('/lecturers?search=charles');
        $response->assertStatus(200);
        $response->assertSee('Charles Xavier');
        $response->assertSee('LEC-1001');
    }

    public function test_lecturer_can_be_edited(): void
    {
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);

        $user = User::create([
            'name' => 'Charles Xavier',
            'email' => 'charles@test.com',
            'password' => bcrypt('password'),
        ]);
        $lecturer = \App\Models\Lecturer::create([
            'user_id' => $user->id,
            'lecturer_id' => 'LEC-1001',
            'qualification' => 'M.Sc.',
            'department_id' => $department->id,
            'salary' => 4000,
            'phone' => '+123456789',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        // Verify edit screen can be rendered
        $response = $this->actingAs($admin)->get("/lecturers/{$lecturer->id}/edit");
        $response->assertStatus(200);

        // Update lecturer profile
        $response = $this->actingAs($admin)->put("/lecturers/{$lecturer->id}", [
            'name' => 'Charles Xavier Updated',
            'email' => 'charles.new@test.com',
            'qualification' => 'Ph.D. in Computer Science',
            'department_id' => $department->id,
            'salary' => 6500,
            'phone' => '+987654321',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('lecturers.show', $lecturer->id));

        // Assert databases updated
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Charles Xavier Updated',
            'email' => 'charles.new@test.com',
        ]);

        $this->assertDatabaseHas('lecturers', [
            'id' => $lecturer->id,
            'qualification' => 'Ph.D. in Computer Science',
            'salary' => 6500.00,
            'phone' => '+987654321',
        ]);
    }

    public function test_lecturer_subject_assignment(): void
    {
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);
        $course = Course::create(['department_id' => $department->id, 'course_code' => 'CS-BSC', 'name' => 'B.Sc. CS']);
        
        $user = User::create([
            'name' => 'Charles Xavier',
            'email' => 'charles@test.com',
            'password' => bcrypt('password'),
        ]);
        $lecturer = \App\Models\Lecturer::create([
            'user_id' => $user->id,
            'lecturer_id' => 'LEC-1001',
            'qualification' => 'Ph.D.',
            'department_id' => $department->id,
            'salary' => 5000,
            'phone' => '+123456789',
            'status' => 'active',
        ]);

        // Create subject
        $subject = Subject::create([
            'course_id' => $course->id,
            'subject_code' => 'CS-101',
            'name' => 'Intro to Programming',
            'credits' => 3,
            'semester' => 1,
            'department_id' => $department->id,
            'lecturer_id' => null,
        ]);

        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        // Verify assignment view shows subject
        $response = $this->actingAs($admin)->get("/lecturers/{$lecturer->id}/assign-subjects");
        $response->assertStatus(200);
        $response->assertSee('CS-101');

        // Assign subject to lecturer
        $response = $this->actingAs($admin)->post("/lecturers/{$lecturer->id}/assign-subjects", [
            'subject_ids' => [$subject->id],
        ]);

        $response->assertRedirect(route('lecturers.show', $lecturer->id));
        
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'lecturer_id' => $lecturer->id,
        ]);
    }

    public function test_admin_can_create_department(): void
    {
        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        $response = $this->actingAs($admin)->from('/lecturers')->post('/departments', [
            'faculty_name' => 'Engineering Faculty Group',
            'name' => 'Mechanical Engineering',
            'code' => 'ME',
            'description' => 'Thermodynamics and design',
        ]);

        $response->assertRedirect('/lecturers');
        $this->assertDatabaseHas('departments', [
            'name' => 'Mechanical Engineering',
            'code' => 'ME',
        ]);
        
        $this->assertDatabaseHas('faculties', [
            'name' => 'Engineering Faculty Group',
        ]);
    }

    public function test_admin_can_create_subject(): void
    {
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);

        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        $response = $this->actingAs($admin)->from('/lecturers')->post('/subjects', [
            'course_name' => 'B.Sc. in Computer Science',
            'subject_code' => 'ME-101',
            'name' => 'Thermodynamics',
            'credits' => 3,
            'semester' => 1,
            'department_id' => $department->id,
        ]);

        $response->assertRedirect('/lecturers');
        $this->assertDatabaseHas('subjects', [
            'subject_code' => 'ME-101',
            'name' => 'Thermodynamics',
        ]);

        $this->assertDatabaseHas('courses', [
            'name' => 'B.Sc. in Computer Science',
            'department_id' => $department->id,
        ]);
    }

    public function test_admin_can_export_customized_students(): void
    {
        $faculty = Faculty::create(['name' => 'Engineering', 'code' => 'ENG']);
        $department1 = Department::create(['faculty_id' => $faculty->id, 'name' => 'Computer Science', 'code' => 'CSE']);
        $department2 = Department::create(['faculty_id' => $faculty->id, 'name' => 'Electrical Engineering', 'code' => 'EEE']);

        $user1 = User::create(['name' => 'Alice Student', 'email' => 'alice@test.com', 'password' => bcrypt('password')]);
        $student1 = Student::create([
            'user_id' => $user1->id,
            'student_id' => 'STU-0001',
            'gender' => 'female',
            'dob' => '2005-01-01',
            'phone' => '12345',
            'address' => 'Test',
            'department_id' => $department1->id,
            'enrollment_date' => '2025-09-01',
            'status' => 'active',
        ]);

        $user2 = User::create(['name' => 'Bob Student', 'email' => 'bob@test.com', 'password' => bcrypt('password')]);
        $student2 = Student::create([
            'user_id' => $user2->id,
            'student_id' => 'STU-0002',
            'gender' => 'male',
            'dob' => '2005-01-01',
            'phone' => '67890',
            'address' => 'Test',
            'department_id' => $department2->id,
            'enrollment_date' => '2025-09-01',
            'status' => 'suspended',
        ]);

        $admin = User::create([
            'name' => 'Registrar Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach(Role::where('slug', 'super_admin')->first()->id);

        // Export customized students (CSE department only, name & email columns only)
        $response = $this->actingAs($admin)->get('/reports/export/students?' . http_build_query([
            'department_id' => $department1->id,
            'columns' => ['name', 'email']
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        
        // Assert header row has selected columns
        $this->assertStringContainsString('"Full Name",Email', $content);
        
        // Assert Alice is present
        $this->assertStringContainsString('"Alice Student",alice@test.com', $content);
        
        // Assert Bob (different department) is absent
        $this->assertStringNotContainsString('Bob Student', $content);
    }
}
