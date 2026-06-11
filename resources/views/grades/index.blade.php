<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Gradebook Entry') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Subject & Assessment Selector -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <form method="GET" action="{{ route('grades.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Subject Course</label>
                        <select name="subject_id" onchange="this.form.submit()" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">-- Choose Subject --</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                                    {{ $subj->subject_code }} - {{ $subj->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Assessment / Exam</label>
                        <select name="exam_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Subject Overall Grade (Final Course Mark)</option>
                            @foreach($exams as $ex)
                                <option value="{{ $ex->id }}" {{ request('exam_id') == $ex->id ? 'selected' : '' }}>
                                    {{ $ex->name }} ({{ ucfirst($ex->type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Load Student Register
                        </button>
                    </div>
                </form>
            </div>

            <!-- Grade Entry sheet -->
            @if($selectedSubject)
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-6">
                    <div>
                        <h3 class="text-md font-bold text-gray-950 dark:text-white">{{ $selectedSubject->name }} ({{ $selectedSubject->subject_code }})</h3>
                        <p class="text-xs text-gray-400 mt-1">Record marks. The system will automatically compute letter grades and GPA scales.</p>
                    </div>

                    <form method="POST" action="{{ route('grades.store') }}">
                        @csrf
                        <input type="hidden" name="subject_id" value="{{ $selectedSubject->id }}" />
                        <input type="hidden" name="exam_id" value="{{ request('exam_id') }}" />

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-400 uppercase">
                                    <tr>
                                        <th class="px-6 py-4">Student ID</th>
                                        <th class="px-6 py-4">Student Name</th>
                                        <th class="px-6 py-4 text-center">Marks (0 - 100)</th>
                                        <th class="px-6 py-4 text-center">Current Scale</th>
                                        <th class="px-6 py-4 text-center">GPA Value</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                    @forelse($students as $student)
                                        @php
                                            $currentGrade = $student->grade_entry;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $student->student_id }}</td>
                                            <td class="px-6 py-4 text-gray-800 dark:text-gray-200">{{ $student->user->name }}</td>
                                            <td class="px-6 py-4 text-center flex justify-center">
                                                <input type="number" step="0.1" name="marks[{{ $student->id }}]" value="{{ $currentGrade?->marks_obtained }}" min="0" max="100" class="w-24 text-center rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                                            </td>
                                            <td class="px-6 py-4 text-center font-bold text-indigo-600 dark:text-indigo-400">
                                                {{ $currentGrade?->grade_letter ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-center text-gray-400">
                                                {{ $currentGrade ? number_format($currentGrade->gpa_value, 2) : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No students enrolled in this course subject.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(count($students) > 0)
                            <div class="pt-6 flex justify-end border-t dark:border-gray-700 mt-6">
                                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                                    Record Grades & Publish
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
