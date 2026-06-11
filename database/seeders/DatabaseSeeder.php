<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Staff;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\FaceRecord;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\Hostel;
use App\Models\Room;
use App\Models\RoomAllocation;
use App\Models\Payroll;
use App\Models\LeaveRequest;
use App\Models\CourseMaterial;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ForumTopic;
use App\Models\ForumReply;
use App\Models\AuditLog;
use App\Models\Event;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $roles = [
            'super_admin' => 'Super Administrator',
            'registrar' => 'Registrar / Admin',
            'lecturer' => 'Lecturer',
            'student' => 'Student',
            'staff' => 'General Staff',
        ];

        $roleModels = [];
        foreach ($roles as $slug => $name) {
            $roleModels[$slug] = Role::create([
                'name' => $name,
                'slug' => $slug,
                'description' => "System role for $name",
            ]);
        }

        // 2. Permissions
        $permissions = [
            'manage_system' => 'Manage Entire System',
            'manage_registrar' => 'Manage Academic Records',
            'manage_grades' => 'Upload and Manage Grades',
            'manage_attendance' => 'Track and Record Attendance',
            'view_student_portal' => 'Access Student Portal',
            'view_lecturer_portal' => 'Access Lecturer Portal',
            'view_staff_portal' => 'Access Staff Portal',
            'manage_finance' => 'Manage Financial Transactions',
        ];

        $permModels = [];
        foreach ($permissions as $slug => $name) {
            $permModels[$slug] = Permission::create([
                'name' => $name,
                'slug' => $slug,
                'description' => "Permission to $name",
            ]);
        }

        // Link Permissions to Roles
        $roleModels['super_admin']->permissions()->sync(array_values($permModels));
        $roleModels['registrar']->permissions()->sync([
            $permModels['manage_registrar']->id,
            $permModels['manage_attendance']->id,
            $permModels['manage_finance']->id,
        ]);
        $roleModels['lecturer']->permissions()->sync([
            $permModels['manage_grades']->id,
            $permModels['manage_attendance']->id,
            $permModels['view_lecturer_portal']->id,
        ]);
        $roleModels['student']->permissions()->sync([
            $permModels['view_student_portal']->id,
        ]);
        $roleModels['staff']->permissions()->sync([
            $permModels['view_staff_portal']->id,
        ]);

        // 3. Create Users
        $usersData = [
            [
                'name' => 'Dr. Eleanor Vance',
                'email' => 'admin@university.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin'
            ],
            [
                'name' => 'Marcus Brody',
                'email' => 'registrar@university.com',
                'password' => Hash::make('password'),
                'role' => 'registrar'
            ],
            [
                'name' => 'Prof. Charles Xavier',
                'email' => 'charles@university.com',
                'password' => Hash::make('password'),
                'role' => 'lecturer'
            ],
            [
                'name' => 'Dr. Bruce Banner',
                'email' => 'bruce@university.com',
                'password' => Hash::make('password'),
                'role' => 'lecturer'
            ],
            [
                'name' => 'Alice Smith',
                'email' => 'alice@university.com',
                'password' => Hash::make('password'),
                'role' => 'student'
            ],
            [
                'name' => 'Bob Jones',
                'email' => 'bob@university.com',
                'password' => Hash::make('password'),
                'role' => 'student'
            ],
            [
                'name' => 'Sarah Connor',
                'email' => 'staff@university.com',
                'password' => Hash::make('password'),
                'role' => 'staff'
            ]
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
            ]);
            $user->roles()->attach($roleModels[$data['role']]->id);
            $users[$data['role']][] = $user;
        }

        // 4. Faculties and Departments
        $facultyEng = Faculty::create(['name' => 'Faculty of Engineering & Technology', 'code' => 'ENG', 'description' => 'Engineering and Applied Sciences']);
        $facultySci = Faculty::create(['name' => 'Faculty of Science', 'code' => 'SCI', 'description' => 'Pure and Natural Sciences']);
        $facultyBiz = Faculty::create(['name' => 'Faculty of Business Administration', 'code' => 'BIZ', 'description' => 'Management, Economics and Finance']);

        $deptCS = Department::create(['faculty_id' => $facultyEng->id, 'name' => 'Computer Science & Engineering', 'code' => 'CSE', 'description' => 'Computing disciplines']);
        $deptEE = Department::create(['faculty_id' => $facultyEng->id, 'name' => 'Electrical & Electronics Engineering', 'code' => 'EEE', 'description' => 'Electrical engineering']);
        $deptMath = Department::create(['faculty_id' => $facultySci->id, 'name' => 'Mathematics & Statistics', 'code' => 'MATH', 'description' => 'Mathematical sciences']);
        $deptFin = Department::create(['faculty_id' => $facultyBiz->id, 'name' => 'Finance & Accounting', 'code' => 'FIN', 'description' => 'Financial systems']);

        // 5. Academic Years
        $academicYear = AcademicYear::create([
            'name' => '2025-2026 Academic Year',
            'start_date' => '2025-09-01',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        // 6. Profiles
        // Student Profiles
        $studentAlice = Student::create([
            'user_id' => $users['student'][0]->id,
            'student_id' => 'STU-2025-0001',
            'gender' => 'female',
            'dob' => '2005-04-12',
            'phone' => '+15550199',
            'address' => '456 College Lane, Dorm B, Room 204',
            'department_id' => $deptCS->id,
            'enrollment_date' => '2025-09-01',
            'status' => 'active',
            'photo_path' => null,
            'academic_history' => ['high_school' => 'Oakridge Academy', 'gpa' => '3.85'],
        ]);

        $studentBob = Student::create([
            'user_id' => $users['student'][1]->id,
            'student_id' => 'STU-2025-0002',
            'gender' => 'male',
            'dob' => '2004-11-23',
            'phone' => '+15550188',
            'address' => '789 University Way, Suite 12',
            'department_id' => $deptEE->id,
            'enrollment_date' => '2025-09-01',
            'status' => 'active',
            'photo_path' => null,
            'academic_history' => ['high_school' => 'Pinecrest High', 'gpa' => '3.50'],
        ]);

        // Lecturer Profiles
        $lecturerCharles = Lecturer::create([
            'user_id' => $users['lecturer'][0]->id,
            'lecturer_id' => 'LEC-1001',
            'qualification' => 'Ph.D. in Computer Science (Stanford)',
            'department_id' => $deptCS->id,
            'salary' => 8500.00,
            'phone' => '+15551001',
            'status' => 'active',
        ]);

        $lecturerBruce = Lecturer::create([
            'user_id' => $users['lecturer'][1]->id,
            'lecturer_id' => 'LEC-1002',
            'qualification' => 'Ph.D. in Nuclear Physics (Caltech)',
            'department_id' => $deptEE->id,
            'salary' => 9000.00,
            'phone' => '+15551002',
            'status' => 'active',
        ]);

        // Staff Profiles
        $staffSarah = Staff::create([
            'user_id' => $users['staff'][0]->id,
            'staff_id' => 'STF-5001',
            'department_id' => $deptCS->id,
            'designation' => 'Senior Administrative Assistant',
            'phone' => '+15555001',
            'salary' => 4500.00,
            'status' => 'active',
        ]);

        // 7. Courses and Subjects
        $courseCS = Course::create([
            'department_id' => $deptCS->id,
            'course_code' => 'CS-BSC',
            'name' => 'B.Sc. in Computer Science',
            'description' => 'Comprehensive study of computer software, hardware, systems and theory',
            'duration_years' => 4,
        ]);

        $courseEE = Course::create([
            'department_id' => $deptEE->id,
            'course_code' => 'EE-BENG',
            'name' => 'B.Eng. in Electrical Engineering',
            'description' => 'Applied electrical systems, power engineering, electronics and control systems',
            'duration_years' => 4,
        ]);

        // Subjects
        $subjectDB = Subject::create([
            'course_id' => $courseCS->id,
            'subject_code' => 'CS-301',
            'name' => 'Database Management Systems',
            'credits' => 4,
            'semester' => 1,
            'department_id' => $deptCS->id,
            'lecturer_id' => $lecturerCharles->id,
        ]);

        $subjectSE = Subject::create([
            'course_id' => $courseCS->id,
            'subject_code' => 'CS-302',
            'name' => 'Software Engineering',
            'credits' => 3,
            'semester' => 1,
            'department_id' => $deptCS->id,
            'lecturer_id' => $lecturerCharles->id,
        ]);

        $subjectCircuits = Subject::create([
            'course_id' => $courseEE->id,
            'subject_code' => 'EE-201',
            'name' => 'Electric Circuits & Networks',
            'credits' => 4,
            'semester' => 1,
            'department_id' => $deptEE->id,
            'lecturer_id' => $lecturerBruce->id,
        ]);

        // 8. Enrollments
        Enrollment::create([
            'student_id' => $studentAlice->id,
            'subject_id' => $subjectDB->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 1,
            'status' => 'approved',
            'approved_by' => $users['registrar'][0]->id,
        ]);

        Enrollment::create([
            'student_id' => $studentAlice->id,
            'subject_id' => $subjectSE->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 1,
            'status' => 'approved',
            'approved_by' => $users['registrar'][0]->id,
        ]);

        Enrollment::create([
            'student_id' => $studentBob->id,
            'subject_id' => $subjectCircuits->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 1,
            'status' => 'approved',
            'approved_by' => $users['registrar'][0]->id,
        ]);

        // 9. Attendance
        $attendanceDates = ['2026-06-01', '2026-06-02', '2026-06-03', '2026-06-04', '2026-06-05', '2026-06-08'];
        foreach ($attendanceDates as $d) {
            Attendance::create([
                'student_id' => $studentAlice->id,
                'subject_id' => $subjectDB->id,
                'date' => $d,
                'check_in' => '09:05:00',
                'check_out' => '10:55:00',
                'status' => $d == '2026-06-03' ? 'absent' : ($d == '2026-06-05' ? 'late' : 'present'),
                'method' => 'qr',
                'device' => 'iPhone 14',
                'location' => 'Lecture Hall 101',
            ]);

            Attendance::create([
                'student_id' => $studentAlice->id,
                'subject_id' => $subjectSE->id,
                'date' => $d,
                'check_in' => '11:02:00',
                'check_out' => '12:30:00',
                'status' => 'present',
                'method' => 'face',
                'device' => 'Lecturer iPad',
                'location' => 'CS Lab A',
            ]);

            Attendance::create([
                'student_id' => $studentBob->id,
                'subject_id' => $subjectCircuits->id,
                'date' => $d,
                'check_in' => '13:00:00',
                'check_out' => '14:50:00',
                'status' => $d == '2026-06-04' ? 'absent' : 'present',
                'method' => 'manual',
                'device' => 'Web Browser',
                'location' => 'Engineering Hall 3',
            ]);
        }

        // 10. Face Records (Mock 128 elements float array as JSON)
        $descriptor = array_map(fn() => rand(-1000, 1000) / 1000, range(1, 128));
        FaceRecord::create([
            'user_id' => $users['student'][0]->id, // Alice
            'face_descriptor' => json_encode($descriptor),
            'photo_path' => 'faces/student_alice.jpg',
        ]);

        // 11. Exams
        $examMidDB = Exam::create([
            'subject_id' => $subjectDB->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Midterm Examination',
            'type' => 'midterm',
            'exam_date' => '2025-11-15',
            'max_marks' => 100,
            'invigilator_id' => $lecturerBruce->id, // Bruce invigilates CS exam
        ]);

        $examFinalDB = Exam::create([
            'subject_id' => $subjectDB->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Final Theory Examination',
            'type' => 'final',
            'exam_date' => '2026-06-10',
            'max_marks' => 100,
            'invigilator_id' => $lecturerBruce->id,
        ]);

        $examMidCircuits = Exam::create([
            'subject_id' => $subjectCircuits->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Midterm Exam',
            'type' => 'midterm',
            'exam_date' => '2025-11-18',
            'max_marks' => 100,
            'invigilator_id' => $lecturerCharles->id,
        ]);

        // 12. Grades
        // Alice grades
        Grade::create([
            'student_id' => $studentAlice->id,
            'subject_id' => $subjectDB->id,
            'exam_id' => $examMidDB->id,
            'marks_obtained' => 92.50,
            'grade_letter' => 'A',
            'gpa_value' => 4.00,
            'semester' => 1,
            'academic_year_id' => $academicYear->id,
        ]);

        Grade::create([
            'student_id' => $studentAlice->id,
            'subject_id' => $subjectDB->id,
            'exam_id' => null, // Subject Overall grade
            'marks_obtained' => 88.00,
            'grade_letter' => 'A',
            'gpa_value' => 4.00,
            'semester' => 1,
            'academic_year_id' => $academicYear->id,
        ]);

        Grade::create([
            'student_id' => $studentAlice->id,
            'subject_id' => $subjectSE->id,
            'exam_id' => null,
            'marks_obtained' => 78.50,
            'grade_letter' => 'B+',
            'gpa_value' => 3.50,
            'semester' => 1,
            'academic_year_id' => $academicYear->id,
        ]);

        // Bob grades
        Grade::create([
            'student_id' => $studentBob->id,
            'subject_id' => $subjectCircuits->id,
            'exam_id' => $examMidCircuits->id,
            'marks_obtained' => 74.00,
            'grade_letter' => 'B',
            'gpa_value' => 3.00,
            'semester' => 1,
            'academic_year_id' => $academicYear->id,
        ]);

        // 13. Fees & Payments
        $feeAlice = Fee::create([
            'student_id' => $studentAlice->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 1,
            'total_amount' => 3500.00,
            'scholarship_amount' => 1000.00,
            'discount_amount' => 200.00,
            'due_date' => '2025-10-01',
            'status' => 'paid',
        ]);

        Payment::create([
            'fee_id' => $feeAlice->id,
            'amount' => 2300.00,
            'payment_date' => '2025-09-28',
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'TXN-901847120',
            'receipt_no' => 'REC-2025-0001',
        ]);

        $feeBob = Fee::create([
            'student_id' => $studentBob->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 1,
            'total_amount' => 4000.00,
            'scholarship_amount' => 0.00,
            'discount_amount' => 0.00,
            'due_date' => '2025-10-01',
            'status' => 'partially_paid',
        ]);

        Payment::create([
            'fee_id' => $feeBob->id,
            'amount' => 2000.00,
            'payment_date' => '2025-09-30',
            'payment_method' => 'credit_card',
            'transaction_reference' => 'TXN-901847125',
            'receipt_no' => 'REC-2025-0002',
        ]);

        // 14. Library Books
        $book1 = Book::create(['title' => 'Introduction to Algorithms', 'author' => 'Thomas H. Cormen', 'isbn' => '978-0262033848', 'category' => 'Computer Science', 'total_copies' => 5, 'available_copies' => 4, 'location_shelf' => 'CS-01']);
        $book2 = Book::create(['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'isbn' => '978-0132350884', 'category' => 'Software Engineering', 'total_copies' => 3, 'available_copies' => 2, 'location_shelf' => 'CS-02']);
        $book3 = Book::create(['title' => 'Standard Handbook for Electrical Engineers', 'author' => 'Donald G. Fink', 'isbn' => '978-0071441469', 'category' => 'Electrical Engineering', 'total_copies' => 2, 'available_copies' => 2, 'location_shelf' => 'EE-04']);

        // Borrow Log
        BookBorrow::create([
            'book_id' => $book1->id,
            'user_id' => $users['student'][0]->id, // Alice
            'borrow_date' => '2026-05-20',
            'due_date' => '2026-06-03',
            'return_date' => '2026-06-02',
            'fine_amount' => 0.00,
            'status' => 'returned',
        ]);

        BookBorrow::create([
            'book_id' => $book2->id,
            'user_id' => $users['student'][0]->id, // Alice
            'borrow_date' => '2026-06-01',
            'due_date' => '2026-06-15',
            'return_date' => null,
            'fine_amount' => 0.00,
            'status' => 'borrowed',
        ]);

        // 15. Hostels & Rooms
        $hostelNewton = Hostel::create(['name' => 'Isaac Newton Residence Hall', 'type' => 'male', 'address' => 'North Campus Sector A']);
        $hostelCurie = Hostel::create(['name' => 'Marie Curie Residence Hall', 'type' => 'female', 'address' => 'North Campus Sector B']);

        $roomN101 = Room::create(['hostel_id' => $hostelNewton->id, 'room_number' => '101', 'capacity' => 2, 'occupants_count' => 1, 'fee_per_semester' => 800.00]);
        $roomC204 = Room::create(['hostel_id' => $hostelCurie->id, 'room_number' => '204', 'capacity' => 2, 'occupants_count' => 1, 'fee_per_semester' => 850.00]);

        // Room allocations
        RoomAllocation::create([
            'room_id' => $roomC204->id,
            'student_id' => $studentAlice->id,
            'academic_year_id' => $academicYear->id,
            'semester' => 1,
            'allocated_date' => '2025-09-01',
            'vacated_date' => null,
            'status' => 'active',
        ]);

        // 16. HR & Payroll
        Payroll::create([
            'user_id' => $users['lecturer'][0]->id, // Charles
            'month' => 5,
            'year' => 2026,
            'basic_salary' => 8500.00,
            'allowances' => 500.00,
            'deductions' => 200.00,
            'net_salary' => 8800.00,
            'payment_date' => '2026-05-31',
            'status' => 'paid',
        ]);

        Payroll::create([
            'user_id' => $users['staff'][0]->id, // Sarah
            'month' => 5,
            'year' => 2026,
            'basic_salary' => 4500.00,
            'allowances' => 250.00,
            'deductions' => 100.00,
            'net_salary' => 4650.00,
            'payment_date' => '2026-05-31',
            'status' => 'paid',
        ]);

        // Leave Requests
        LeaveRequest::create([
            'user_id' => $users['lecturer'][1]->id, // Bruce
            'leave_type' => 'sick',
            'start_date' => '2026-06-04',
            'end_date' => '2026-06-05',
            'reason' => 'Severe influenza, advised absolute bed rest',
            'status' => 'approved',
            'approved_by' => $users['super_admin'][0]->id,
        ]);

        LeaveRequest::create([
            'user_id' => $users['staff'][0]->id, // Sarah
            'leave_type' => 'casual',
            'start_date' => '2026-06-15',
            'end_date' => '2026-06-16',
            'reason' => 'Family event',
            'status' => 'pending',
            'approved_by' => null,
        ]);

        // 17. Course Materials
        CourseMaterial::create([
            'subject_id' => $subjectDB->id,
            'lecturer_id' => $lecturerCharles->id,
            'title' => 'Lecture 1: Relational Algebra',
            'description' => 'Slide deck and reading materials for database fundamentals',
            'file_path' => 'materials/relational_algebra.pdf',
        ]);

        CourseMaterial::create([
            'subject_id' => $subjectDB->id,
            'lecturer_id' => $lecturerCharles->id,
            'title' => 'Lecture 2: Normalization (1NF, 2NF, 3NF, BCNF)',
            'description' => 'Complete guide to database schema normalization theories with practice exercises',
            'file_path' => 'materials/normalization_notes.pdf',
        ]);

        // 18. Assignments
        $assignmentDB = Assignment::create([
            'subject_id' => $subjectDB->id,
            'title' => 'SQL Project: E-Commerce Store Design',
            'description' => 'Develop a complete SQL script modeling a store, with at least 8 tables and indexes.',
            'due_date' => '2026-06-20 23:59:00',
            'max_marks' => 100,
            'file_path' => 'assignments/sql_project_guidelines.pdf',
        ]);

        // Submission
        AssignmentSubmission::create([
            'assignment_id' => $assignmentDB->id,
            'student_id' => $studentAlice->id,
            'submission_date' => '2026-06-08 14:22:00',
            'file_path' => 'submissions/alice_sql_project.zip',
            'marks_obtained' => null, // Not graded yet
            'feedback' => null,
        ]);

        // 19. Discussions
        $topic = ForumTopic::create([
            'subject_id' => $subjectDB->id,
            'user_id' => $users['student'][0]->id, // Alice
            'title' => 'Questions on Boyce-Codd Normal Form (BCNF) anomalies',
            'content' => 'In slide 14, how do we identify if a dependency violates BCNF compared to 3NF? Can anyone clarify?',
        ]);

        ForumReply::create([
            'topic_id' => $topic->id,
            'user_id' => $users['lecturer'][0]->id, // Prof Charles
            'content' => 'Hi Alice, remember BCNF requires that for any X -> Y, X must be a superkey. Under 3NF, Y is allowed to be a prime attribute, which BCNF prohibits.',
        ]);

        // 20. Events
        Event::create([
            'title' => 'Annual convocation ceremony 2026',
            'description' => 'Celebrating the graduating class of 2026 at the Main Arena.',
            'start_date' => '2026-07-15 09:00:00',
            'end_date' => '2026-07-15 14:00:00',
            'location' => 'Main Auditorium Arena',
            'audience' => 'all',
        ]);

        Event::create([
            'title' => 'Lecturer Training Workshop',
            'description' => 'Introduction to the new AI Attendance features and LMS systems.',
            'start_date' => '2026-06-12 14:00:00',
            'end_date' => '2026-06-12 17:00:00',
            'location' => 'Senate Hall Block B',
            'audience' => 'lecturers',
        ]);

        // 21. Audit Logs
        AuditLog::create([
            'user_id' => $users['super_admin'][0]->id,
            'action' => 'login',
            'description' => 'Super admin logged into the administration console',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);
        
        AuditLog::create([
            'user_id' => $users['lecturer'][0]->id,
            'action' => 'publish_grades',
            'description' => 'Lecturer published grades for Midterm DBMS exam',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ]);
    }
}
