<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Student Financial Ledger') }}
            </h2>
            <a href="{{ route('finance.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                + Generate Fee Invoice
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

            <!-- Search and Filter -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <form method="GET" action="{{ route('finance.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Search Students</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, Email, or Student ID..." class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Filter Invoice Status</label>
                        <select name="status" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partially_paid" {{ request('status') === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        </select>
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Apply Filters
                        </button>
                        <a href="{{ route('finance.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm text-center">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Student ID</th>
                                <th class="px-6 py-4">Student Name</th>
                                <th class="px-6 py-4">Period</th>
                                <th class="px-6 py-4">Net Billed</th>
                                <th class="px-6 py-4">Total Paid</th>
                                <th class="px-6 py-4">Due Date</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                            @forelse($fees as $fee)
                                @php
                                    $net = $fee->total_amount - $fee->scholarship_amount - $fee->discount_amount;
                                    $paid = $fee->payments->sum('amount');
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $fee->student->student_id }}</td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">{{ $fee->student->user->name }}</td>
                                    <td class="px-6 py-4 text-xs">Sem {{ $fee->semester }} ({{ $fee->academicYear->name }})</td>
                                    <td class="px-6 py-4 font-semibold">${{ number_format($net, 2) }}</td>
                                    <td class="px-6 py-4 font-semibold text-emerald-600">${{ number_format($paid, 2) }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-400">{{ $fee->due_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                                            {{ $fee->status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : ($fee->status === 'partially_paid' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400') }}">
                                            {{ str_replace('_', ' ', $fee->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($fee->status !== 'paid')
                                            <a href="{{ route('finance.payment', $fee->id) }}" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-semibold transition">
                                                Record Payment
                                            </a>
                                        @else
                                            @if($fee->payments->count() > 0)
                                                <a href="{{ route('finance.receipt', $fee->payments->last()->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950 dark:text-indigo-400 rounded text-xs font-semibold">
                                                    Receipt
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">Fully Paid</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No student financial ledgers logged.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-150 dark:border-gray-700">
                    {{ $fees->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
