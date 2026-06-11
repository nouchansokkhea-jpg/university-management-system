<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Academic Portal') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Student Metrics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- GPA Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-indigo-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">Cumulative GPA</span>
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($gpa, 2) }}</span>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                </div>

                <!-- Attendance Card -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-emerald-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">Attendance Rate</span>
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $attRate }}%</span>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Outstanding Fees -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-rose-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">Outstanding Fees</span>
                        <span class="text-3xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">${{ number_format($totalDue, 2) }}</span>
                    </div>
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/50 text-rose-600 dark:text-rose-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>

                <!-- Books Borrowed -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-amber-600 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block">Library Borrowed</span>
                        <span class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $borrows->count() }} Books</span>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400 rounded-lg">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Main Portal Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Enrolled Subjects -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm md:col-span-2 space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Active Registered Subjects</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 bg-gray-50 dark:bg-gray-900 dark:text-gray-400 uppercase">
                                <tr>
                                    <th class="px-4 py-3">Code</th>
                                    <th class="px-4 py-3">Subject Name</th>
                                    <th class="px-4 py-3">Credits</th>
                                    <th class="px-4 py-3">Professor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @forelse($enrollments as $enr)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $enr->subject->subject_code }}</td>
                                        <td class="px-4 py-3">{{ $enr->subject->name }}</td>
                                        <td class="px-4 py-3">{{ $enr->subject->credits }} Credits</td>
                                        <td class="px-4 py-3 text-xs">
                                            @if($enr->subject->lecturer)
                                                <span class="font-medium block">{{ $enr->subject->lecturer->user->name }}</span>
                                                <span class="text-gray-400">{{ $enr->subject->lecturer->user->email }}</span>
                                            @else
                                                <span class="text-gray-400">Not Assigned</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">No subject registrations approved for this semester.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Face Recognition Check & Action -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm flex flex-col justify-between space-y-4">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">AI Face Verification</h4>
                        <p class="text-sm text-gray-500 mt-1">Register your facial descriptor vectors to allow touchless checks and anti-spoofing logins on campus kiosks.</p>
                    </div>
                    
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-950/50 rounded-lg flex items-center space-x-3">
                        <span class="p-2 bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-400 rounded-full">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            @if(Auth::user()->faceRecord)
                                <span class="text-xs font-semibold text-emerald-600 block">Biometrics Registered</span>
                                <span class="text-[10px] text-gray-400">128-d feature matrix stored.</span>
                            @else
                                <span class="text-xs font-semibold text-rose-600 block">Biometrics Missing</span>
                                <span class="text-[10px] text-gray-400">Click register below to activate.</span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('attendance.face-register') }}" class="w-full text-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                        Configure Biometrics Face
                    </a>
                </div>
            </div>

            <!-- Bottom Section (Hostel & Library & Events) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Hostel details -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Hostel Accommodation</h4>
                    @if($allocation)
                        <div class="space-y-2">
                            <div class="flex justify-between border-b pb-1 dark:border-gray-700">
                                <span class="text-sm text-gray-500">Residence Hall</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $allocation->room->hostel->name }}</span>
                            </div>
                            <div class="flex justify-between border-b pb-1 dark:border-gray-700">
                                <span class="text-sm text-gray-500">Room Number</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">Room {{ $allocation->room->room_number }}</span>
                            </div>
                            <div class="flex justify-between pb-1">
                                <span class="text-sm text-gray-500">Status</span>
                                <span class="text-xs font-semibold text-emerald-600 uppercase bg-emerald-50 dark:bg-emerald-950/50 px-2 py-0.5 rounded">Active Allocation</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No hostel room allocated for this semester.</p>
                    @endif
                </div>

                <!-- Library Books Loaned -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Borrowed Library Books</h4>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse($borrows as $borr)
                            <div class="py-2 first:pt-0 last:pb-0 flex justify-between items-center text-xs">
                                <div>
                                    <span class="font-semibold text-gray-900 dark:text-white block">{{ $borr->book->title }}</span>
                                    <span class="text-gray-400 mt-1 block">Due: {{ $borr->due_date->format('M d, Y') }}</span>
                                </div>
                                @if($borr->due_date->isPast())
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400 rounded text-[10px] font-semibold">Overdue</span>
                                @else
                                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-400 rounded text-[10px] font-semibold">Checked out</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-4">No books currently loaned out.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Campus Events -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Upcoming Events</h4>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse($events as $event)
                            <div class="py-2.5 first:pt-0 last:pb-0 text-xs">
                                <h5 class="font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h5>
                                <p class="text-gray-400 mt-1">{{ $event->start_date->format('M d, Y @ H:i') }} | {{ $event->location }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 py-4">No upcoming student events.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
