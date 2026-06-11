<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\FaceRecord;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    /**
     * Show manual attendance recording sheet.
     */
    public function index(Request $request)
    {
        $subjects = Subject::with('course')->get();
        $selectedSubject = null;
        $students = [];
        $date = $request->input('date', date('Y-m-d'));

        if ($request->filled('subject_id')) {
            $selectedSubject = Subject::findOrFail($request->input('subject_id'));
            
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

            // Get all students enrolled in this subject
            $studentIds = Enrollment::where('subject_id', $selectedSubject->id)
                ->where('status', 'approved')
                ->pluck('student_id');

            $students = Student::whereIn('id', $studentIds)->with('user')->get();

            // Load existing attendance for this date
            $existingAttendance = Attendance::where('subject_id', $selectedSubject->id)
                ->where('date', $date)
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $student->attendance_today = $existingAttendance->get($student->id);
            }
        }

        return view('attendance.index', compact('subjects', 'selectedSubject', 'students', 'date'));
    }

    /**
     * Save manual attendance.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:present,absent,late,excused'],
        ]);

        $subjectId = $request->subject_id;
        $date = $request->date;
        $statuses = $request->status; // key: student_id, value: status

        foreach ($statuses as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                    'method' => 'manual',
                    'device' => 'Web Admin Panel',
                    'location' => 'Classroom',
                ]
            );
        }

        return redirect()->route('attendance.index', ['subject_id' => $subjectId, 'date' => $date])
            ->with('success', 'Attendance saved successfully.');
    }

    /**
     * Display QR Code generator for classroom check-in.
     */
    public function showQrGenerator(Request $request)
    {
        $subjects = Subject::with('course')->get();
        $selectedSubject = null;
        $qrPayload = null;

        if ($request->filled('subject_id')) {
            $selectedSubject = Subject::findOrFail($request->subject_id);
            $date = date('Y-m-d');
            $expires = time() + 300; // 5 minutes validity

            // Encrypted string representing: subject_id | date | expiry
            $rawPayload = $selectedSubject->id . '|' . $date . '|' . $expires;
            $qrPayload = Crypt::encryptString($rawPayload);
        }

        return view('attendance.qr-generator', compact('subjects', 'selectedSubject', 'qrPayload'));
    }

    /**
     * Student scans QR Code payload via mobile portal.
     */
    public function scanQr(Request $request)
    {
        $request->validate([
            'qr_payload' => ['required', 'string'],
            'device' => ['required', 'string'],
            'location' => ['nullable', 'string'], // e.g. "lat: 13.7563, lng: 100.5018"
        ]);

        try {
            $decrypted = Crypt::decryptString($request->qr_payload);
            list($subjectId, $date, $expires) = explode('|', $decrypted);

            // Check expiry
            if (time() > (int)$expires) {
                return response()->json(['success' => false, 'message' => 'QR Code has expired.'], 400);
            }

            $user = $request->user();
            $student = $user->student;

            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Only registered students can check-in.'], 403);
            }

            // Verify student is enrolled in this subject
            $isEnrolled = Enrollment::where('student_id', $student->id)
                ->where('subject_id', $subjectId)
                ->where('status', 'approved')
                ->exists();

            if (!$isEnrolled) {
                return response()->json(['success' => false, 'message' => 'You are not enrolled in this course subject.'], 403);
            }

            // Record attendance
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                    'date' => $date,
                ],
                [
                    'check_in' => date('H:i:s'),
                    'status' => 'present',
                    'method' => 'qr',
                    'device' => $request->device,
                    'location' => $request->location ?? 'GPS Classroom Coordinates',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Attendance logged successfully via QR Code scan.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid QR Code payload.'], 400);
        }
    }

    /**
     * Display Face Recognition registration page.
     */
    public function showFaceRegistration()
    {
        return view('attendance.face-register');
    }

    /**
     * Register facial vector descriptor for user.
     */
    public function registerFace(Request $request)
    {
        $request->validate([
            'face_descriptor' => ['required', 'array', 'size:128'],
            'face_descriptor.*' => ['numeric'],
            'photo' => ['nullable', 'string'], // Base64 image
        ]);

        $user = $request->user();

        $photoPath = null;
        if ($request->filled('photo')) {
            $imageData = $request->photo; // Base64
            $image = str_replace('data:image/jpeg;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $imageName = 'face_' . $user->id . '_' . time() . '.jpg';
            
            Storage::disk('public')->put('faces/' . $imageName, base64_decode($image));
            $photoPath = 'faces/' . $imageName;
        }

        FaceRecord::updateOrCreate(
            ['user_id' => $user->id],
            [
                'face_descriptor' => $request->face_descriptor,
                'photo_path' => $photoPath,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Face characteristics registered successfully.'
        ]);
    }

    /**
     * AI Face recognition check-in screen.
     */
    public function showFaceVerification()
    {
        $subjects = Subject::with('course')->get();
        return view('attendance.face-verify', compact('subjects'));
    }

    /**
     * Match face descriptor vector and check in user.
     */
    public function verifyFace(Request $request)
    {
        $request->validate([
            'face_descriptor' => ['required', 'array', 'size:128'],
            'face_descriptor.*' => ['numeric'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'device' => ['required', 'string'],
        ]);

        $inputVector = $request->face_descriptor;
        $matchingUser = null;
        $minDistance = 999.0;
        $matchThreshold = 0.55; // 0.6 standard, 0.55 more secure

        // Fetch all registered face records
        $records = FaceRecord::all();

        foreach ($records as $record) {
            $savedVector = $record->face_descriptor; // Automatically casted to array
            if (!is_array($savedVector) || count($savedVector) !== 128) {
                continue;
            }

            // Calculate Euclidean Distance
            $sumSq = 0.0;
            for ($i = 0; $i < 128; $i++) {
                $diff = $inputVector[$i] - $savedVector[$i];
                $sumSq += $diff * $diff;
            }
            $distance = sqrt($sumSq);

            if ($distance < $minDistance) {
                $minDistance = $distance;
                if ($distance < $matchThreshold) {
                    $matchingUser = User::find($record->user_id);
                }
            }
        }

        if (!$matchingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Face verification failed: Person not recognized. Match score: ' . round($minDistance, 3)
            ], 401);
        }

        // Check if the user is a student
        $student = $matchingUser->student;
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Face verified for: ' . $matchingUser->name . ' but user is not registered as a student.'
            ], 403);
        }

        // Verify enrollment in this subject
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Verified: ' . $matchingUser->name . '. However, student is not enrolled in this subject.'
            ], 403);
        }

        // Perform Check-in or Check-out
        $today = date('Y-m-d');
        $attendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->check_in && !$attendance->check_out) {
            // Already checked in, perform Check-out
            $attendance->update([
                'check_out' => date('H:i:s'),
                'device' => $request->device,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out completed for student: ' . $matchingUser->name . ' at ' . date('H:i:s'),
                'name' => $matchingUser->name,
                'type' => 'checkout'
            ]);
        } else {
            // Log fresh check-in
            Attendance::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $request->subject_id,
                    'date' => $today,
                ],
                [
                    'check_in' => date('H:i:s'),
                    'status' => 'present',
                    'method' => 'face',
                    'device' => $request->device,
                    'location' => 'AI Verification Portal Room 104',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-in completed for student: ' . $matchingUser->name . ' at ' . date('H:i:s'),
                'name' => $matchingUser->name,
                'type' => 'checkin'
            ]);
        }
    }
}
