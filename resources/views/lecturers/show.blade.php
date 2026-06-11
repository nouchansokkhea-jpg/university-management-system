<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Lecturer Faculty Profile') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('lecturers.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                    Back to Registry
                </a>
                <a href="{{ route('lecturers.edit', $lecturer->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                    Edit Profile
                </a>
                <a href="{{ route('lecturers.assign-subjects', $lecturer->id) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded font-semibold text-sm transition">
                    Assign Subjects
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 1. General Demographics Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden p-6 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="flex flex-col items-center space-y-3">
                    <span class="h-28 w-28 rounded-xl bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 flex items-center justify-center font-bold text-4xl border-2 border-indigo-600/30">
                        {{ strtoupper(substr($lecturer->user->name, 0, 2)) }}
                    </span>
                    <div class="text-center">
                        <h3 class="text-lg font-bold text-gray-950 dark:text-white">{{ $lecturer->user->name }}</h3>
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold tracking-wider uppercase block">{{ $lecturer->lecturer_id }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider mt-2 inline-block
                            {{ $lecturer->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400' }}">
                            {{ $lecturer->status }}
                        </span>
                    </div>
                </div>

                <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-700 dark:text-gray-300">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Department Assignment</span>
                        <span class="font-medium text-gray-950 dark:text-white">{{ $lecturer->department->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Qualification</span>
                        <span class="font-medium text-gray-950 dark:text-white">{{ $lecturer->qualification }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Faculty Group</span>
                        <span class="font-medium text-gray-950 dark:text-white">{{ $lecturer->department->faculty->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Faculty Email</span>
                        <span>{{ $lecturer->user->email }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Phone Contact</span>
                        <span>{{ $lecturer->phone }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Active Base Salary</span>
                        <span class="font-semibold text-emerald-600">${{ number_format($lecturer->salary, 2) }} / mo</span>
                    </div>
                </div>
            </div>

            <!-- 2. Course subjects assigned -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subjects list -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-base font-bold text-gray-800 dark:text-white">Active Subjects Assigned</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-[10px] text-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-400 uppercase">
                                <tr>
                                    <th class="px-4 py-3">Code</th>
                                    <th class="px-4 py-3">Subject</th>
                                    <th class="px-4 py-3">Course</th>
                                    <th class="px-4 py-3">Credits</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @forelse($lecturer->subjects as $subj)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $subj->subject_code }}</td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $subj->name }}</td>
                                        <td class="px-4 py-3">{{ $subj->course->name }}</td>
                                        <td class="px-4 py-3">{{ $subj->credits }} Credits</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">No active subjects mapped.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Exam scheduling and materials -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-base font-bold text-gray-800 dark:text-white">Examination Schedules (Invigilations)</h4>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse($lecturer->exams as $exam)
                            <div class="py-3 first:pt-0 last:pb-0 flex justify-between items-center text-xs">
                                <div>
                                    <span class="font-semibold text-gray-950 dark:text-white block">{{ $exam->name }}</span>
                                    <span class="text-gray-400">{{ $exam->subject->name }} ({{ $exam->subject->subject_code }})</span>
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-900 px-2.5 py-1 rounded">
                                    {{ $exam->exam_date->format('Y-m-d') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 py-4 text-center">No invigilating duties logged.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
