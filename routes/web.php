<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ReportingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard Route - Shared Auth
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Auth Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Role-Protected UMS Web Routes
Route::middleware(['auth'])->group(function () {

    // 1. Super Admin & Registrar / Academic Admin Operations
    Route::middleware(['role:super_admin,registrar'])->group(function () {
        // Students Management CRUD
        Route::resource('students', StudentController::class);
        
        // Lecturers Management CRUD & Subject Mapping
        Route::resource('lecturers', LecturerController::class);
        Route::get('lecturers/{lecturer}/assign-subjects', [LecturerController::class, 'showAssignSubjects'])->name('lecturers.assign-subjects');
        Route::post('lecturers/{lecturer}/assign-subjects', [LecturerController::class, 'assignSubjects']);
        Route::post('departments', [LecturerController::class, 'storeDepartment'])->name('departments.store');
        Route::post('subjects', [LecturerController::class, 'storeSubject'])->name('subjects.store');

        // Financial & Tuition Billing
        Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
        Route::get('finance/invoice/create', [FinanceController::class, 'create'])->name('finance.create');
        Route::post('finance/invoice', [FinanceController::class, 'store'])->name('finance.store');

        // Human Resource, Employee payroll & Leave Approvals
        Route::get('hr/payroll', [HRController::class, 'payrollIndex'])->name('hr.payroll.index');
        Route::get('hr/payroll/create', [HRController::class, 'payrollCreate'])->name('hr.payroll.create');
        Route::post('hr/payroll', [HRController::class, 'payrollStore'])->name('hr.payroll.store');
        
        Route::get('hr/leaves', [HRController::class, 'leaveIndex'])->name('hr.leaves.index');
        Route::post('hr/leaves/{leave}/approve', [HRController::class, 'leaveApprove'])->name('hr.leaves.approve');

        // Library Catalog Management & Loans
        Route::get('library/books/create', [LibraryController::class, 'create'])->name('library.books.create');
        Route::post('library/books', [LibraryController::class, 'store'])->name('library.books.store');
        Route::get('library/borrows', [LibraryController::class, 'borrowsIndex'])->name('library.borrows.index');
        Route::get('library/borrows/create', [LibraryController::class, 'borrowCreate'])->name('library.borrows.create');
        Route::post('library/borrows', [LibraryController::class, 'borrowStore'])->name('library.borrows.store');
        Route::post('library/borrows/{borrow}/return', [LibraryController::class, 'returnBook'])->name('library.borrows.return');

        // Reporting Modules & Data Exporting
        Route::get('reports', [ReportingController::class, 'index'])->name('reports.index');
        Route::get('reports/export/{type}', [ReportingController::class, 'export'])->name('reports.export');
    });

    // 2. Shared Lecturers, Admins & Registrars
    Route::middleware(['role:super_admin,registrar,lecturer'])->group(function () {
        // Attendance Management
        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('attendance/qr-generator', [AttendanceController::class, 'showQrGenerator'])->name('attendance.qr-generator');
        Route::get('attendance/face-verify', [AttendanceController::class, 'showFaceVerification'])->name('attendance.face-verify');
        
        // Grade inputting and publishing
        Route::get('grades', [GradeController::class, 'index'])->name('grades.index');
        Route::post('grades', [GradeController::class, 'store'])->name('grades.store');
    });

    // 3. Shared Students, Lecturers, Staff (Student Invoicing/Transcript & Employee Leaves)
    // Student Fee Payment & Transcripts
    Route::get('finance/invoice/{fee}/payment', [FinanceController::class, 'showPaymentForm'])->name('finance.payment');
    Route::post('finance/invoice/{fee}/payment', [FinanceController::class, 'storePayment']);
    Route::get('finance/receipt/{payment}', [FinanceController::class, 'receipt'])->name('finance.receipt');
    Route::get('students/{student}/transcript', [GradeController::class, 'transcript'])->name('grades.transcript');

    // Library book searching
    Route::get('library/books', [LibraryController::class, 'index'])->name('library.books.index');

    // Leaves Request submission for staff/lecturers
    Route::get('hr/leaves/apply', [HRController::class, 'leaveCreate'])->name('hr.leaves.create');
    Route::post('hr/leaves/apply', [HRController::class, 'leaveStore'])->name('hr.leaves.store');

    // Face Recognition Registration & Verification ajax
    Route::get('attendance/face-register', [AttendanceController::class, 'showFaceRegistration'])->name('attendance.face-register');
    Route::post('api/attendance/register-face', [AttendanceController::class, 'registerFace'])->name('api.attendance.register-face');
    Route::post('api/attendance/verify-face', [AttendanceController::class, 'verifyFace'])->name('api.attendance.verify-face');
    Route::post('api/attendance/scan-qr', [AttendanceController::class, 'scanQr'])->name('api.attendance.scan-qr');
});

require __DIR__.'/auth.php';
