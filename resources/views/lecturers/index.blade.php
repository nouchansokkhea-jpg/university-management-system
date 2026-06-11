<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Lecturer Faculty Registry') }}
            </h2>
            <div class="flex space-x-2">
                <button onclick="document.getElementById('add-department-modal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-semibold text-sm transition">
                    + Add Department
                </button>
                <button onclick="document.getElementById('add-subject-modal').classList.remove('hidden')" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded font-semibold text-sm transition">
                    + Add Subject
                </button>
                <a href="{{ route('lecturers.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                    + Register New Lecturer
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

            <!-- Search and Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <form method="GET" action="{{ route('lecturers.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Search Faculty</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email, ID, or Qualification..." class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Filter Department</label>
                        <select name="department_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }} ({{ $dept->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('lecturers.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Lecturers Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Lecturer ID</th>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Department</th>
                                <th class="px-6 py-4">Qualification</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                            @forelse($lecturers as $lect)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $lect->lecturer_id }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-900 dark:text-white block">{{ $lect->user->name }}</span>
                                        <span class="text-xs text-gray-400 block">{{ $lect->user->email }}</span>
                                    </td>
                                    <td class="px-6 py-4">{{ $lect->department->name }} ({{ $lect->department->code }})</td>
                                    <td class="px-6 py-4 text-xs max-w-[200px] truncate" title="{{ $lect->qualification }}">{{ $lect->qualification }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                                            {{ $lect->status === 'active' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400' }}">
                                            {{ $lect->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center flex items-center justify-center space-x-2">
                                        <a href="{{ route('lecturers.show', $lect->id) }}" class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950 dark:hover:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded text-xs font-semibold">
                                            Profile
                                        </a>
                                        <a href="{{ route('lecturers.edit', $lect->id) }}" class="px-2.5 py-1.5 bg-sky-50 hover:bg-sky-100 dark:bg-sky-950 dark:hover:bg-sky-900 text-sky-600 dark:text-sky-400 rounded text-xs font-semibold">
                                            Edit
                                        </a>
                                        <a href="{{ route('lecturers.assign-subjects', $lect->id) }}" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 dark:bg-amber-950 dark:hover:bg-amber-900 text-amber-600 dark:text-amber-400 rounded text-xs font-semibold">
                                            Subjects
                                        </a>
                                        <form method="POST" action="{{ route('lecturers.destroy', $lect->id) }}" onsubmit="return confirm('Are you sure you want to delete this lecturer?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950 dark:hover:bg-rose-900 text-rose-600 dark:text-rose-400 rounded text-xs font-semibold">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">No lecturers currently registered matching filters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-150 dark:border-gray-700">
                    {{ $lecturers->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Add Department Modal -->
    <div id="add-department-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 max-w-md w-full shadow-2xl space-y-6">
            <div class="flex justify-between items-center border-b pb-4 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">Add New Department</h3>
                <button onclick="document.getElementById('add-department-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('departments.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Faculty Group</label>
                    <input list="faculties-list" name="faculty_name" required placeholder="Select or type Faculty Group..." class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    <datalist id="faculties-list">
                        @foreach($faculties as $fac)
                            <option value="{{ $fac->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Department Name</label>
                    <input type="text" name="name" required placeholder="e.g. Mechanical Engineering" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Department Code (Unique)</label>
                    <input type="text" name="code" required placeholder="e.g. ME" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                    <textarea name="description" rows="3" placeholder="Department details..." class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500"></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('add-department-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-semibold text-sm">
                        Add Department
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Subject Modal -->
    <div id="add-subject-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-8 max-w-md w-full shadow-2xl space-y-6">
            <div class="flex justify-between items-center border-b pb-4 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-950 dark:text-white">Add New Subject</h3>
                <button onclick="document.getElementById('add-subject-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('subjects.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Associated Course</label>
                    <input list="courses-list" name="course_name" required placeholder="Select or type Associated Course..." class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    <datalist id="courses-list">
                        @foreach($courses as $crs)
                            <option value="{{ $crs->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Subject Code (Unique)</label>
                    <input type="text" name="subject_code" required placeholder="e.g. ME-101" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Subject Name</label>
                    <input type="text" name="name" required placeholder="e.g. Thermodynamics" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Credits</label>
                        <input type="number" name="credits" required min="1" max="10" value="3" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Semester (1-8)</label>
                        <input type="number" name="semester" required min="1" max="8" value="1" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Department Assignment</label>
                    <select name="department_id" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Assign Lecturer (Optional)</label>
                    <select name="lecturer_id" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                        <option value="">Leave Unassigned</option>
                        @foreach($allLecturers as $lec)
                            <option value="{{ $lec->id }}">{{ $lec->user->name }} ({{ $lec->lecturer_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                    <button type="button" onclick="document.getElementById('add-subject-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded font-semibold text-sm">
                        Add Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
