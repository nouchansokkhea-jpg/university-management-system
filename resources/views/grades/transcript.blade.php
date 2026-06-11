<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Official Academic Transcript') }}
            </h2>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                Print Transcript
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen printable-container">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm space-y-8 border print:border-0">
                
                <!-- University Header (Visible when printed) -->
                <div class="text-center space-y-2 border-b-2 border-gray-200 dark:border-gray-700 pb-6">
                    <h1 class="text-2xl font-extrabold tracking-wider text-gray-900 dark:text-white uppercase">Apex University of Technology</h1>
                    <span class="text-xs text-gray-400">Office of the Registrar • official academic transcript</span>
                </div>

                <!-- Student information block -->
                <div class="grid grid-cols-2 gap-6 text-sm text-gray-700 dark:text-gray-300">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Student Name</span>
                        <span class="font-bold text-gray-950 dark:text-white text-base">{{ $student->user->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Student ID Number</span>
                        <span class="font-bold text-indigo-600 dark:text-indigo-400 text-base">{{ $student->student_id }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Major Degree Course</span>
                        <span class="font-medium text-gray-950 dark:text-white">{{ $student->department->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Enrollment Date</span>
                        <span>{{ $student->enrollment_date->format('F d, Y') }}</span>
                    </div>
                </div>

                <!-- Semesters breakdown -->
                @forelse($grades as $semester => $semesterGrades)
                    <div class="space-y-3">
                        <h3 class="text-sm font-extrabold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider border-b dark:border-gray-700 pb-1">
                            Semester {{ $semester }} Summary
                        </h3>
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-[10px] text-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-400 uppercase">
                                <tr>
                                    <th class="px-4 py-2.5">Code</th>
                                    <th class="px-4 py-2.5">Subject Name</th>
                                    <th class="px-4 py-2.5 text-center">Credits</th>
                                    <th class="px-4 py-2.5 text-center">Grade</th>
                                    <th class="px-4 py-2.5 text-center">Grade Point</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @foreach($semesterGrades as $grade)
                                    <tr>
                                        <td class="px-4 py-2.5 font-semibold text-gray-900 dark:text-white">{{ $grade->subject->subject_code }}</td>
                                        <td class="px-4 py-2.5 text-gray-700 dark:text-gray-300">{{ $grade->subject->name }}</td>
                                        <td class="px-4 py-2.5 text-center">{{ $grade->subject->credits }}</td>
                                        <td class="px-4 py-2.5 text-center font-bold text-gray-900 dark:text-white">{{ $grade->grade_letter }}</td>
                                        <td class="px-4 py-2.5 text-center font-semibold text-indigo-600 dark:text-indigo-400">{{ number_format($grade->gpa_value, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-6">No academic marks entered on the official transcript yet.</p>
                @endforelse

                <!-- Summary statistics block -->
                <div class="border-t-2 border-gray-200 dark:border-gray-700 pt-6 grid grid-cols-3 gap-6 bg-indigo-50/50 dark:bg-indigo-950/20 p-6 rounded-xl">
                    <div class="text-center">
                        <span class="text-xs text-gray-400 uppercase tracking-wider block">Total Credits Earned</span>
                        <span class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalCredits }}</span>
                    </div>
                    <div class="text-center">
                        <span class="text-xs text-gray-400 uppercase tracking-wider block">Scale Value</span>
                        <span class="text-xl font-bold text-gray-900 dark:text-white mt-1">4.00</span>
                    </div>
                    <div class="text-center">
                        <span class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block font-bold">Cumulative CGPA</span>
                        <span class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1">{{ number_format($cgpa, 2) }}</span>
                    </div>
                </div>

                <!-- Registrar Sign Off footer (printed only) -->
                <div class="pt-12 flex justify-between text-xs text-gray-400 hidden print:flex">
                    <div>
                        <span class="block">Date of Issue: {{ date('Y-m-d') }}</span>
                    </div>
                    <div class="text-center space-y-6">
                        <div class="w-48 border-b border-gray-300"></div>
                        <span>Authorized Registrar Signature</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Print styling support -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-container, .printable-container * {
                visibility: visible;
            }
            .printable-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            header, nav {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
