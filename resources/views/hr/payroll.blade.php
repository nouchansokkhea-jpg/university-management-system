<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('HR Employee Payroll Registry') }}
            </h2>
            <a href="{{ route('hr.payroll.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                + Process Pay Slip
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Payroll History -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Employee Name</th>
                                <th class="px-6 py-4">Pay Period</th>
                                <th class="px-6 py-4">Base Salary</th>
                                <th class="px-6 py-4">Allowances</th>
                                <th class="px-6 py-4">Deductions</th>
                                <th class="px-6 py-4">Net Salary</th>
                                <th class="px-6 py-4">Disbursement Date</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                            @forelse($payrolls as $pay)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $pay->user->name }}</td>
                                    <td class="px-6 py-4">
                                        {{ date('F', mktime(0, 0, 0, $pay->month, 1)) }} {{ $pay->year }}
                                    </td>
                                    <td class="px-6 py-4">${{ number_format($pay->basic_salary, 2) }}</td>
                                    <td class="px-6 py-4 text-emerald-600">+${{ number_format($pay->allowances, 2) }}</td>
                                    <td class="px-6 py-4 text-rose-500">-${{ number_format($pay->deductions, 2) }}</td>
                                    <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">${{ number_format($pay->net_salary, 2) }}</td>
                                    <td class="px-6 py-4 text-xs">{{ $pay->payment_date ? $pay->payment_date->format('Y-m-d') : 'Pending' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                                            {{ $pay->status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400' }}">
                                            {{ $pay->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No payroll entries processed.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-150 dark:border-gray-700">
                    {{ $payrolls->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
