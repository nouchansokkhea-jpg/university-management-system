<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Official Transaction Receipt') }}
            </h2>
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                Print Receipt
            </button>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen printable-container">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm border space-y-6 print:border-0">
                
                <div class="text-center space-y-2 border-b pb-6 dark:border-gray-700">
                    <h1 class="text-lg font-extrabold text-gray-900 dark:text-white uppercase tracking-wider">Apex University Treasury Office</h1>
                    <span class="text-xs text-gray-400">Official tuition payment receipt</span>
                </div>

                <!-- Receipt meta -->
                <div class="grid grid-cols-2 gap-4 text-xs text-gray-600 dark:text-gray-400">
                    <div>
                        <span class="block"><strong>Receipt No:</strong> {{ $payment->receipt_no }}</span>
                        <span class="block"><strong>Transaction Reference:</strong> {{ $payment->transaction_reference ?? 'Cash Payment' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="block"><strong>Date of Payment:</strong> {{ $payment->payment_date->format('Y-m-d') }}</span>
                        <span class="block"><strong>Payment Method:</strong> {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}</span>
                    </div>
                </div>

                <!-- Student info -->
                <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg text-xs grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-400 block">Billed To</span>
                        <strong class="text-gray-800 dark:text-gray-200 text-sm block mt-1">{{ $payment->fee->student->user->name }}</strong>
                        <span class="text-[10px] text-gray-400">ID: {{ $payment->fee->student->student_id }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-gray-400 block">Enrollment Term</span>
                        <span class="font-semibold block mt-1">Semester {{ $payment->fee->semester }}</span>
                        <span class="text-[10px] text-gray-400">{{ $payment->fee->academicYear->name }}</span>
                    </div>
                </div>

                <!-- Transaction ledger details -->
                <div class="space-y-3 text-xs">
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700">
                        <span class="text-gray-400">Tuition Fee Invoice Billed</span>
                        <span class="font-semibold">${{ number_format($payment->fee->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700 text-emerald-600">
                        <span>Scholarship & Allowances Deduction</span>
                        <span>-${{ number_format($payment->fee->scholarship_amount + $payment->fee->discount_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2 dark:border-gray-700 text-indigo-600 dark:text-indigo-400 font-bold">
                        <span>Amount Received (This Transaction)</span>
                        <span>${{ number_format($payment->amount, 2) }}</span>
                    </div>
                    
                    @php
                        $net = $payment->fee->total_amount - $payment->fee->scholarship_amount - $payment->fee->discount_amount;
                        $totalPaid = $payment->fee->payments->where('payment_date', '<=', $payment->payment_date)->sum('amount');
                        $balance = max(0, $net - $totalPaid);
                    @endphp

                    <div class="flex justify-between font-bold text-gray-900 dark:text-white">
                        <span>Remaining Balance Outstanding</span>
                        <span>${{ number_format($balance, 2) }}</span>
                    </div>
                </div>

                <div class="border-t pt-6 text-center text-[10px] text-gray-400 dark:border-gray-700">
                    <p>Thank you for your transaction. If you have any billing queries, please contact treasury@university.edu.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Print support styling -->
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
