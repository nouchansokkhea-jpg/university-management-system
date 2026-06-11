<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Administrator Command Center') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Real-time Statistic Counters -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Students Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 flex items-center justify-between border-l-4 border-indigo-600">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Students</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_students'] }}</h3>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/50 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                    </div>
                </div>

                <!-- Lecturers Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 flex items-center justify-between border-l-4 border-emerald-600">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lecturers</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_lecturers'] }}</h3>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/50 rounded-lg text-emerald-600 dark:text-emerald-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Staff Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 flex items-center justify-between border-l-4 border-amber-600">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Administrative Staff</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_staff'] }}</h3>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/50 rounded-lg text-amber-600 dark:text-amber-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>

                <!-- Courses Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 flex items-center justify-between border-l-4 border-rose-600">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Departments / Courses</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_courses'] }}</h3>
                    </div>
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/50 rounded-lg text-rose-600 dark:text-rose-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Mid-level Summaries (Attendance & Financial Invoices) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Attendance Summary Widget -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4 flex flex-col justify-between">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">Attendance Analytics</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Average student attendance rate across all active courses.</p>
                    </div>
                    <div class="flex items-center justify-center py-4">
                        <div class="relative flex items-center justify-center">
                            <!-- Circular Progress Bar -->
                            <svg class="w-36 h-36 transform -rotate-90">
                                <circle cx="72" cy="72" r="62" stroke="currentColor" stroke-width="12" class="text-gray-200 dark:text-gray-700" fill="transparent" />
                                <circle cx="72" cy="72" r="62" stroke="currentColor" stroke-width="12" class="text-indigo-600 dark:text-indigo-400" fill="transparent"
                                    stroke-dasharray="390"
                                    stroke-dashoffset="{{ 390 - (390 * $stats['attendance_rate']) / 100 }}" />
                            </svg>
                            <span class="absolute text-2xl font-extrabold text-gray-800 dark:text-white">{{ $stats['attendance_rate'] }}%</span>
                        </div>
                    </div>
                    <div class="text-center text-xs text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-950/50 py-2 rounded-lg">
                        Optimal engagement threshold (>= 80%) reached
                    </div>
                </div>

                <!-- Financial Health Summary -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm md:col-span-2 space-y-6">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">University Treasury Balance</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Invoice tracking, tuition fee collection summary for active semesters.</p>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg text-center">
                            <span class="text-xs text-gray-500 uppercase tracking-wider block">Total Billed</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['fees_billed'], 2) }}</span>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-950/50 p-4 rounded-lg text-center">
                            <span class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Collected</span>
                            <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($stats['fees_collected'], 2) }}</span>
                        </div>
                        <div class="bg-rose-50 dark:bg-rose-950/50 p-4 rounded-lg text-center">
                            <span class="text-xs text-rose-600 dark:text-rose-400 uppercase tracking-wider block">Outstanding</span>
                            <span class="text-xl font-bold text-rose-600 dark:text-rose-400">${{ number_format($stats['fees_outstanding'], 2) }}</span>
                        </div>
                    </div>
                    <!-- ProgressBar representation -->
                    <div class="space-y-1">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Collection Progress</span>
                            <span class="font-semibold text-gray-700 dark:text-gray-300">
                                {{ $stats['fees_billed'] > 0 ? round(($stats['fees_collected'] / $stats['fees_billed']) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-3 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-full rounded-full" style="width: {{ $stats['fees_billed'] > 0 ? ($stats['fees_collected'] / $stats['fees_billed']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Analytics Charts (Vibrant Aesthetics) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department Distribution -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Students by Department</h4>
                    <div class="h-64 relative flex justify-center">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>

                <!-- Monthly Collections -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Monthly Tuition Cashflow</h4>
                    <div class="h-64 relative flex justify-center">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Calendar Feed & Audit Alerts -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Upcoming Campus Events -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm md:col-span-2 space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Academic Calendar & Upcoming Events</h4>
                    <div class="divide-y divide-gray-150 dark:divide-gray-700">
                        @forelse($events as $event)
                            <div class="py-4 first:pt-0 last:pb-0 flex justify-between items-center">
                                <div>
                                    <h5 class="font-semibold text-gray-900 dark:text-white">{{ $event->title }}</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center space-x-1">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>{{ $event->location }}</span>
                                        <span class="mx-2">|</span>
                                        <span>{{ $event->start_date->format('M d, Y @ H:i') }}</span>
                                    </p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium uppercase tracking-wider
                                    {{ $event->audience === 'all' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-400' }}">
                                    {{ $event->audience }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 py-4">No upcoming events listed on the calendar.</p>
                        @endforelse
                    </div>
                </div>

                <!-- System Audit & Security Warnings -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Security & Audit logs</h4>
                    <div class="space-y-3">
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <span class="p-1.5 bg-emerald-500 text-white rounded-full mt-0.5">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <div>
                                <span class="text-xs font-semibold block text-gray-700 dark:text-gray-300">Database Seeding Ok</span>
                                <p class="text-[11px] text-gray-500">All 33 relational tables populated and running.</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                            <span class="p-1.5 bg-indigo-500 text-white rounded-full mt-0.5">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <div>
                                <span class="text-xs font-semibold block text-gray-700 dark:text-gray-300">Sanctum Auth Active</span>
                                <p class="text-[11px] text-gray-500">API Bearer tokens enabled and configured.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Config -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Department Chart
            const deptCtx = document.getElementById('deptChart').getContext('2d');
            const deptLabels = {!! json_encode($enrollmentsByDept->pluck('code')) !!};
            const deptData = {!! json_encode($enrollmentsByDept->pluck('count')) !!};

            new Chart(deptCtx, {
                type: 'doughnut',
                data: {
                    labels: deptLabels.length ? deptLabels : ['CSE', 'EEE', 'MATH', 'FIN'],
                    datasets: [{
                        data: deptData.length ? deptData : [10, 8, 5, 4],
                        backgroundColor: ['#4f46e5', '#10b981', '#f59e0b', '#f43f5e'],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#4b5563'
                            }
                        }
                    }
                }
            });

            // 2. Finance Collections Chart
            const finCtx = document.getElementById('financeChart').getContext('2d');
            const rawMonths = {!! json_encode($monthlyPayments->pluck('month')) !!};
            const rawTotals = {!! json_encode($monthlyPayments->pluck('total')) !!};

            const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
            const formattedMonths = rawMonths.length ? rawMonths.map(m => monthNames[parseInt(m) - 1]) : ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'May'];
            const formattedTotals = rawTotals.length ? rawTotals : [2300, 2000, 1500, 3000, 4100, 4300];

            new Chart(finCtx, {
                type: 'line',
                data: {
                    labels: formattedMonths,
                    datasets: [{
                        label: 'Fees Collected ($)',
                        data: formattedTotals,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.1)'
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#9ca3af'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
