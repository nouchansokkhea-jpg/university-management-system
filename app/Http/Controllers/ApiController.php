<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\FaceRecord;
use App\Models\Grade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    /**
     * API Login - Issuing Sanctum Token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.'
            ], 401);
        }

        // Get user role
        $role = $user->roles()->first()?->slug ?? 'student';

        // Create token
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $role,
            ]
        ]);
    }

    /**
     * API Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revoked successfully.'
        ]);
    }

    /**
     * Fetch authenticated user details
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load(['roles']);
        $role = $user->roles->first()?->slug;

        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $role,
        ];

        if ($role === 'student' && $user->student) {
            $profileData['student_profile'] = $user->student->load('department');
        } elseif ($role === 'lecturer' && $user->lecturer) {
            $profileData['lecturer_profile'] = $user->lecturer->load('department');
        }

        return response()->json([
            'success' => true,
            'profile' => $profileData
        ]);
    }

    /**
     * Fetch all available courses
     */
    public function courses()
    {
        $courses = Course::with('department')->get();
        return response()->json([
            'success' => true,
            'courses' => $courses
        ]);
    }

    /**
     * Fetch subjects assigned to a lecturer or student
     */
    public function subjects(Request $request)
    {
        $user = $request->user();
        $role = $user->roles->first()?->slug;

        if ($role === 'lecturer' && $user->lecturer) {
            $subjects = Subject::where('lecturer_id', $user->lecturer->id)->with('course')->get();
        } elseif ($role === 'student' && $user->student) {
            $enrollments = Enrollment::where('student_id', $user->student->id)
                ->where('status', 'approved')
                ->pluck('subject_id');
            $subjects = Subject::whereIn('id', $enrollments)->with('course')->get();
        } else {
            $subjects = Subject::with('course')->get();
        }

        return response()->json([
            'success' => true,
            'subjects' => $subjects
        ]);
    }

    /**
     * API Face verification (matches scanned vector descriptor)
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
        $matchThreshold = 0.55;

        $records = FaceRecord::all();
        foreach ($records as $record) {
            $savedVector = $record->face_descriptor;
            if (!is_array($savedVector) || count($savedVector) !== 128) {
                continue;
            }

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
                'message' => 'Face not recognized. Match score: ' . round($minDistance, 3)
            ], 401);
        }

        $student = $matchingUser->student;
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Person recognized: ' . $matchingUser->name . ', but they are not a student.'
            ], 403);
        }

        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'Verified ' . $matchingUser->name . ' but student is not enrolled in this course.'
            ], 403);
        }

        $today = date('Y-m-d');
        $attendance = Attendance::where('student_id', $student->id)
            ->where('subject_id', $request->subject_id)
            ->where('date', $today)
            ->first();

        if ($attendance && $attendance->check_in && !$attendance->check_out) {
            $attendance->update([
                'check_out' => date('H:i:s'),
                'device' => $request->device,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Check-out recorded for: ' . $matchingUser->name,
                'name' => $matchingUser->name,
                'type' => 'checkout'
            ]);
        } else {
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
                    'location' => 'Mobile Device Front Camera',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Check-in recorded for: ' . $matchingUser->name,
                'name' => $matchingUser->name,
                'type' => 'checkin'
            ]);
        }
    }

    /**
     * API Student QR Code check-in
     */
    public function scanQr(Request $request)
    {
        $request->validate([
            'qr_payload' => ['required', 'string'],
            'device' => ['required', 'string'],
            'location' => ['nullable', 'string'],
        ]);

        try {
            $decrypted = Crypt::decryptString($request->qr_payload);
            list($subjectId, $date, $expires) = explode('|', $decrypted);

            if (time() > (int)$expires) {
                return response()->json(['success' => false, 'message' => 'QR Code has expired.'], 400);
            }

            $student = $request->user()->student;
            if (!$student) {
                return response()->json(['success' => false, 'message' => 'Authenticated user is not a student.'], 403);
            }

            $isEnrolled = Enrollment::where('student_id', $student->id)
                ->where('subject_id', $subjectId)
                ->where('status', 'approved')
                ->exists();

            if (!$isEnrolled) {
                return response()->json(['success' => false, 'message' => 'Not enrolled in this subject.'], 403);
            }

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
                    'location' => $request->location ?? 'GPS Classroom',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Attendance logged successfully via mobile QR Scan.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid QR Code payload.'], 400);
        }
    }
}
