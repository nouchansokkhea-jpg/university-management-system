<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Classroom Attendance Ledger') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Subject & Date Selector -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <form method="GET" action="{{ route('attendance.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Subject</label>
                        <select name="subject_id" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Choose Subject --</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                                    {{ $subj->subject_code }} - {{ $subj->name }} ({{ $subj->course->course_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Class Date</label>
                        <input type="date" name="date" value="{{ $date }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Load Class Checklist
                        </button>
                    </div>
                </form>
            </div>

            <!-- Student Checklist Sheet -->
            @if($selectedSubject)
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-6">
                    <div>
                        <h3 class="text-md font-bold text-gray-950 dark:text-white">{{ $selectedSubject->name }} ({{ $selectedSubject->subject_code }})</h3>
                        <p class="text-xs text-gray-400 mt-1">Mark attendance for {{ $date }}. Enrolled Class Size: {{ count($students) }} students.</p>
                    </div>

                    <form method="POST" action="{{ route('attendance.store') }}">
                        @csrf
                        <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}" />
                        <input type="hidden" name="date" value="{{ $date }}" />

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-6 py-4">Student ID</th>
                                        <th class="px-6 py-4">Student Name</th>
                                        <th class="px-6 py-4 text-center">Present</th>
                                        <th class="px-6 py-4 text-center">Absent</th>
                                        <th class="px-6 py-4 text-center">Late</th>
                                        <th class="px-6 py-4 text-center">Excused</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                    @forelse($students as $student)
                                        @php
                                            $todayStatus = $student->attendance_today?->status ?? 'present';
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $student->student_id }}</td>
                                            <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $student->user->name }}</td>
                                            <td class="px-6 py-4 text-center">
                                                <input type="radio" name="status[{{ $student->id }}]" value="present" {{ $todayStatus === 'present' ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <input type="radio" name="status[{{ $student->id }}]" value="absent" {{ $todayStatus === 'absent' ? 'checked' : '' }} class="h-4 w-4 text-rose-600 focus:ring-rose-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <input type="radio" name="status[{{ $student->id }}]" value="late" {{ $todayStatus === 'late' ? 'checked' : '' }} class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <input type="radio" name="status[{{ $student->id }}]" value="excused" {{ $todayStatus === 'excused' ? 'checked' : '' }} class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 dark:border-gray-700 dark:bg-gray-900" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No students enrolled in this course subject.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(count($students) > 0)
                            <div class="pt-6 flex justify-end border-t dark:border-gray-700 mt-6">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                                    Save Daily Attendance Sheet
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
