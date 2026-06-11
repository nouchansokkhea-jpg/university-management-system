<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Administrative Staff Portal') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Staff Info Header Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-indigo-600 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block font-semibold">Designation</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $stats['designation'] }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block font-semibold">Department</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $stats['department'] }}</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block font-semibold">Contracted Salary</span>
                    <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-1">${{ number_format($stats['salary'], 2) }} / mo</span>
                </div>
                <div>
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wider block font-semibold">Pending Leaves</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $stats['pending_leaves_count'] }} Requests</span>
                </div>
            </div>

            <!-- Payroll & Leaves Sections -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payroll History -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Recent Payroll History</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Pay Period</th>
                                    <th class="px-4 py-3">Net Salary</th>
                                    <th class="px-4 py-3">Payment Date</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @forelse($payrolls as $pay)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                            {{ date('F', mktime(0, 0, 0, $pay->month, 1)) }} {{ $pay->year }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-emerald-600">${{ number_format($pay->net_salary, 2) }}</td>
                                        <td class="px-4 py-3 text-gray-400">{{ $pay->payment_date ? $pay->payment_date->format('Y-m-d') : 'N/A' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider
                                                {{ $pay->status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400' }}">
                                                {{ $pay->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">No payroll disbursements logged.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Leave History & Apply Button -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">My Leave Requests</h4>
                        <a href="{{ route('hr.leaves.create') }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-xs font-semibold transition">
                            Apply Leave
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-[10px] text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Leave Type</th>
                                    <th class="px-4 py-3">Start / End Date</th>
                                    <th class="px-4 py-3">Reason</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                                @forelse($leaves as $leave)
                                    <tr>
                                        <td class="px-4 py-3 font-semibold uppercase">{{ $leave->leave_type }}</td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                                            {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-4 py-3 max-w-[150px] truncate text-gray-400" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-semibold uppercase tracking-wider
                                                {{ $leave->status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : ($leave->status === 'rejected' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400') }}">
                                                {{ $leave->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-400">No leave requests filed.</td>
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
