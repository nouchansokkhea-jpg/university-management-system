<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('University Reporting & Exports Hub') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">Analytics Export Panels</h3>
                    <p class="text-xs text-gray-400 mt-1">Download university census records, cashflow statements, and student mark checklists in CSV spreadsheet formats.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <!-- Students Report -->
                    <div class="p-6 border border-gray-150 dark:border-gray-700 rounded-xl space-y-4 flex flex-col justify-between bg-gray-50 dark:bg-gray-900/30 md:col-span-2 shadow-sm">
                        <div>
                            <span class="font-bold text-indigo-600 dark:text-indigo-400 text-base block">Student Registrar Report Customizer</span>
                            <p class="text-xs text-gray-400 mt-1">Select filters and specific columns carefully to include in your customized student CSV export.</p>
                        </div>
                        
                        <form method="GET" action="{{ route('reports.export', 'students') }}" class="space-y-4">
                            <!-- Filters -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Filter Department</label>
                                    <select name="department_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-xs focus:ring-indigo-500">
                                        <option value="">All Departments</option>
                                        @foreach(\App\Models\Department::all() as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Filter Status</label>
                                    <select name="status" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-xs focus:ring-indigo-500">
                                        <option value="">All Statuses</option>
                                        <option value="active">Active</option>
                                        <option value="suspended">Suspended</option>
                                        <option value="graduated">Graduated</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Column Checklist -->
                            <div class="space-y-2 pt-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Select Columns to Export</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 text-xs">
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="student_id" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Student ID</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="name" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Full Name</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="email" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Email</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="gender" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Gender</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="phone" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Phone</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="department" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Department</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="enrollment_date" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Enroll Date</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="status" checked class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">Status</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="high_school" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">High School</span>
                                    </label>
                                    <label class="flex items-center space-x-2 p-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                        <input type="checkbox" name="columns[]" value="high_school_gpa" class="rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4" />
                                        <span class="text-gray-700 dark:text-gray-300">High School GPA</span>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="w-full text-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-xs transition mt-4 flex items-center justify-center space-x-2 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Export Selected Student Data</span>
                            </button>
                        </form>
                    </div>

                    <!-- Attendance Report -->
                    <div class="p-6 border border-gray-150 dark:border-gray-700 rounded-xl space-y-3 flex flex-col justify-between bg-gray-50 dark:bg-gray-900/30">
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white text-sm block">Attendance Percentage Summary</span>
                            <p class="text-xs text-gray-400 mt-1">Exposes average attendance rates per student per course, totals checked-in sessions, and absent logs.</p>
                        </div>
                        <a href="{{ route('reports.export', 'attendance') }}" class="w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-xs transition mt-4">
                            Export Attendance CSV
                        </a>
                    </div>

                    <!-- Financial Report -->
                    <div class="p-6 border border-gray-150 dark:border-gray-700 rounded-xl space-y-3 flex flex-col justify-between bg-gray-50 dark:bg-gray-900/30">
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white text-sm block">Treasury & Tuition Balances</span>
                            <p class="text-xs text-gray-400 mt-1">Details student billing ledger items, scholarships allowances, discount codes, paid totals, and outstanding due amounts.</p>
                        </div>
                        <a href="{{ route('reports.export', 'finance') }}" class="w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-xs transition mt-4">
                            Export Finance CSV
                        </a>
                    </div>

                    <!-- Lecturer Workload -->
                    <div class="p-6 border border-gray-150 dark:border-gray-700 rounded-xl space-y-3 flex flex-col justify-between bg-gray-50 dark:bg-gray-900/30">
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white text-sm block">Lecturer Qualifications & Salary</span>
                            <p class="text-xs text-gray-400 mt-1">Lists lecturer names, assigned departments, qualifications, active status, and monthly payroll salaries.</p>
                        </div>
                        <a href="{{ route('reports.export', 'lecturers') }}" class="w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-xs transition mt-4">
                            Export Lecturer CSV
                        </a>
                    </div>

                    <!-- Academic Performance -->
                    <div class="p-6 border border-gray-150 dark:border-gray-700 rounded-xl space-y-3 flex flex-col justify-between bg-gray-50 dark:bg-gray-900/30 md:col-span-2">
                        <div>
                            <span class="font-bold text-gray-900 dark:text-white text-sm block">Academic Grade & GPA Distributions</span>
                            <p class="text-xs text-gray-400 mt-1">Includes finalized subject scores, grade letter symbols (A, B, C, F), and corresponding grade points (4.00, 3.50, etc.).</p>
                        </div>
                        <a href="{{ route('reports.export', 'performance') }}" class="w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-xs transition mt-4">
                            Export Academic Performance CSV
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
