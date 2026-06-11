<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lecturer Portal & Course Hub') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Subjects Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-indigo-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">My Subjects</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['assigned_subjects_count'] }}</span>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>

                <!-- Students Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-emerald-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">Students Enrolled</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_students'] }}</span>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Materials Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-amber-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">Course Materials</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_materials'] }}</span>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <!-- Attendance Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-rose-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">Class Attendance</span>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['attendance_rate'] }}%</span>
                    </div>
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Assigned Subjects & Course Hub -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-6">
                <div>
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Assigned Subject Hub</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Mark attendance sheets, edit grade registers, or run interactive classrooms.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Subject Code</th>
                                <th class="px-6 py-4">Subject Name</th>
                                <th class="px-6 py-4">Credits</th>
                                <th class="px-6 py-4">Course / Degree</th>
                                <th class="px-6 py-4 text-center">Classroom Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                            @forelse($subjects as $subj)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $subj->subject_code }}</td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $subj->name }}</td>
                                    <td class="px-6 py-4">{{ $subj->credits }} Credits</td>
                                    <td class="px-6 py-4">{{ $subj->course->name }}</td>
                                    <td class="px-6 py-4 flex items-center justify-center space-x-2">
                                        <a href="{{ route('attendance.index', ['subject_id' => $subj->id]) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold">
                                            Attendance
                                        </a>
                                        <a href="{{ route('grades.index', ['subject_id' => $subj->id]) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                            Gradebook
                                        </a>
                                        <a href="{{ route('attendance.qr-generator', ['subject_id' => $subj->id]) }}" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-semibold">
                                            QR Code
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">No subjects currently assigned for this semester.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Sections (Exams & HR Leaves) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Exam Schedule -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Assigned Exams / Invigilation Duty</h4>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse($upcomingExams as $exam)
                            <div class="py-4 first:pt-0 last:pb-0 flex justify-between items-center">
                                <div>
                                    <h5 class="font-semibold text-gray-900 dark:text-white">{{ $exam->name }}</h5>
                                    <p class="text-xs text-gray-500 mt-1">{{ $exam->subject->name }} ({{ $exam->subject->subject_code }})</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 block">{{ $exam->exam_date->format('M d, Y') }}</span>
                                    <span class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wider font-semibold">{{ $exam->type }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-4">No upcoming examinations assigned for invigilation.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Leaves and Payroll Summary -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">Leave Status</h4>
                        <a href="{{ route('hr.leaves.create') }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950 dark:hover:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded text-xs font-semibold">
                            Request Leave
                        </a>
                    </div>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse($pendingLeaves as $leave)
                            <div class="py-4 first:pt-0 last:pb-0 flex justify-between items-center">
                                <div>
                                    <h5 class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($leave->leave_type) }} Leave</h5>
                                    <p class="text-xs text-gray-500 mt-1">{{ $leave->start_date->format('M d') }} to {{ $leave->end_date->format('M d, Y') }}</p>
                                </div>
                                <span class="px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-4">No pending leave requests.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
