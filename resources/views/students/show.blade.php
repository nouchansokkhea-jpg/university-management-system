<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Profile & Academic Record') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('students.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                    Back to List
                </a>
                <a href="{{ route('students.edit', $student->id) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-semibold text-sm transition">
                    Edit Profile
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 1. Demographics & Main Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="flex flex-col items-center space-y-3">
                    @if($student->photo_path)
                        <img src="{{ asset('storage/' . $student->photo_path) }}" class="h-32 w-32 rounded-xl object-cover border-2 border-indigo-600 shadow-md" alt="Student Photo" />
                    @else
                        <span class="h-32 w-32 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold text-4xl border-2 border-indigo-600/30">
                            {{ strtoupper(substr($student->user->name, 0, 2)) }}
                        </span>
                    @endif
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $student->user->name }}</h3>
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold tracking-wider uppercase block">{{ $student->student_id }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider mt-2 inline-block
                            {{ $student->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400' }}">
                            {{ $student->status }}
                        </span>
                    </div>
                </div>

                <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-700 dark:text-gray-300">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Department</span>
                        <span class="font-medium text-gray-950 dark:text-white">{{ $student->department->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Faculty</span>
                        <span class="font-medium text-gray-950 dark:text-white">{{ $student->department->faculty->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">GPA / CGPA</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400 text-base">{{ number_format($gpa, 2) }} / 4.00</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Email Address</span>
                        <span>{{ $student->user->email }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Phone Number</span>
                        <span>{{ $student->phone }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Gender</span>
                        <span class="capitalize">{{ $student->gender }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Date of Birth</span>
                        <span>{{ $student->dob->format('M d, Y') }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Enrollment Date</span>
                        <span>{{ $student->enrollment_date->format('M d, Y') }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Home Address</span>
                        <span>{{ $student->address }}</span>
                    </div>
                </div>
            </div>

            <!-- 2. Academic Hub & Class Enrollments -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Class Enrollments -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm md:col-span-2 space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Semester Enrollments</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Subject Code</th>
                                    <th class="px-4 py-3">Subject</th>
                                    <th class="px-4 py-3">Credits</th>
                                    <th class="px-4 py-3">Semester</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @forelse($student->enrollments as $enr)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $enr->subject->subject_code }}</td>
                                        <td class="px-4 py-3">{{ $enr->subject->name }}</td>
                                        <td class="px-4 py-3">{{ $enr->subject->credits }} Credits</td>
                                        <td class="px-4 py-3">Semester {{ $enr->semester }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400">
                                                {{ $enr->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">No active subject enrollments.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Academic Transcript Shortcuts -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm flex flex-col justify-between space-y-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">Academic Performance</h4>
                        <p class="text-sm text-gray-500 mt-1">Review student semester distributions, GPAs, and generate complete printable transcripts.</p>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between border-b pb-1 dark:border-gray-700">
                            <span class="text-gray-400">Active GPAs</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($gpa, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b pb-1 dark:border-gray-700">
                            <span class="text-gray-400">Subjects Completed</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $student->grades()->whereNull('exam_id')->count() }} Subjects</span>
                        </div>
                    </div>

                    <a href="{{ route('grades.transcript', $student->id) }}" class="w-full text-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                        View Complete Transcript
                    </a>
                </div>
            </div>

            <!-- 3. Attendance logs & Finance statements -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Attendance Stats -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">Attendance Logs</h4>
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-400">
                            {{ $attendanceRate }}% Attendance Rate
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Subject</th>
                                    <th class="px-4 py-3">Check-in / Out</th>
                                    <th class="px-4 py-3">Method</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @forelse($student->attendance->take(6) as $att)
                                    <tr>
                                        <td class="px-4 py-3">{{ $att->date->format('Y-m-d') }}</td>
                                        <td class="px-4 py-3">{{ $att->subject->subject_code }}</td>
                                        <td class="px-4 py-3">
                                            {{ $att->check_in ?? 'N/A' }} / {{ $att->check_out ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 capitalize">{{ $att->method }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider
                                                {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : ($att->status === 'late' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400') }}">
                                                {{ $att->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-gray-400">No attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Finance Statements -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">Fee Statements</h4>
                        @if($feesOutstanding > 0)
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400">
                                Balance: ${{ number_format($feesOutstanding, 2) }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400">
                                Balance: Paid
                            </span>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Invoice Period</th>
                                    <th class="px-4 py-3">Net Amount</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @forelse($student->fees as $fee)
                                    @php
                                        $net = $fee->total_amount - $fee->scholarship_amount - $fee->discount_amount;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">Semester {{ $fee->semester }} ({{ $fee->academicYear->name }})</td>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">${{ number_format($net, 2) }}</td>
                                        <td class="px-4 py-3 capitalize">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider
                                                {{ $fee->status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400' }}">
                                                {{ $fee->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($fee->status !== 'paid')
                                                <a href="{{ route('finance.payment', $fee->id) }}" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-semibold text-[10px]">
                                                    Pay Invoice
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-[10px]">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">No fee invoices recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
