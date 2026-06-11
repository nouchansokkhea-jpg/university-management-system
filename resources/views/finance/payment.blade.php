<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Process Tuition Invoice Payment') }}
            </h2>
            <a href="{{ route('finance.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                Cancel
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Invoice Details Card (Sidebar) -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm space-y-4">
                <h3 class="text-md font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b pb-2 dark:border-gray-700">Invoice Details</h3>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 block uppercase">Student ID</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $fee->student->student_id }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block uppercase">Student Name</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $fee->student->user->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block uppercase">Billing Period</span>
                        <span>Semester {{ $fee->semester }} ({{ $fee->academicYear->name }})</span>
                    </div>
                    <div class="border-t pt-2 dark:border-gray-700 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Total Billed:</span>
                            <span>${{ number_format($fee->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-emerald-600">
                            <span>Scholarship:</span>
                            <span>-${{ number_format($fee->scholarship_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-emerald-600">
                            <span>Discount:</span>
                            <span>-${{ number_format($fee->discount_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-gray-900 dark:text-white border-t pt-1 dark:border-gray-700">
                            <span>Net Due:</span>
                            <span>${{ number_format($fee->total_amount - $fee->scholarship_amount - $fee->discount_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Process Card (Main Form) -->
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm md:col-span-2 space-y-6">
                <h3 class="text-md font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b pb-2 dark:border-gray-700">Checkout Terminal</h3>

                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('finance.payment', $fee->id) }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Payment Amount ($)</label>
                            <input type="number" step="0.01" name="amount" value="{{ old('amount', $outstanding) }}" max="{{ $outstanding }}" min="1" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                            <span class="text-[10px] text-gray-400 mt-1 block">Maximum remaining outstanding: ${{ number_format($outstanding, 2) }}</span>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Payment Channel</label>
                            <select name="payment_method" id="paymentMethod" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                                <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Cash payment</option>
                                <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Bank Wire Transfer</option>
                                <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>Credit / Debit Card</option>
                                <option value="mobile_payment" {{ old('payment_method') === 'mobile_payment' ? 'selected' : '' }}>Mobile QuickPay QR</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Payment Helpers -->
                    <!-- 1. Bank transfer detail -->
                    <div id="helperBank" class="p-4 bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-900/50 rounded-lg text-xs space-y-2 hidden">
                        <span class="font-bold text-indigo-700 dark:text-indigo-400 block uppercase">University Wire Instructions</span>
                        <p class="text-gray-600 dark:text-gray-300">Transfer payment to: <strong>Apex Technical University Finance</strong></p>
                        <p class="text-gray-600 dark:text-gray-300">Bank: <strong>Metropolitan Academic Trust Bank</strong></p>
                        <p class="text-gray-600 dark:text-gray-300">Account No: <strong>1004-9028-4421-998</strong></p>
                        <p class="text-gray-500 italic mt-1">Please enter the invoice number or student ID as transfer description.</p>
                    </div>

                    <!-- 2. Credit card inputs -->
                    <div id="helperCard" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg space-y-4 hidden">
                        <span class="font-bold text-gray-800 dark:text-gray-200 text-xs block uppercase">Card details</span>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-3">
                                <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">Card Number</label>
                                <input type="text" placeholder="4000 1234 5678 9010" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-xs focus:ring-indigo-500" />
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">Expiration Date</label>
                                <input type="text" placeholder="MM / YY" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-xs focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-gray-400 uppercase mb-1">CVV</label>
                                <input type="text" placeholder="123" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-xs focus:ring-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <!-- 3. Mobile payment QR -->
                    <div id="helperMobile" class="p-4 bg-gray-50 dark:bg-gray-900/50 border rounded-lg flex flex-col items-center text-center space-y-3 hidden">
                        <span class="font-bold text-gray-800 dark:text-gray-200 text-xs block uppercase">Scan QuickPay Code</span>
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=pay_invoice_{{ $fee->id }}" class="h-32 w-32 shadow border" alt="Payment QR" />
                        <p class="text-[10px] text-gray-400 max-w-xs">Scan using your mobile banking application or e-wallet to authorize immediate transfer.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Transaction Reference / Receipt Attachment</label>
                        <input type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" placeholder="e.g. Bank Reference ID, Cheque No, TXN Code..." class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    </div>

                    <div class="pt-6 flex justify-end space-x-3 border-t dark:border-gray-700">
                        <a href="{{ route('finance.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition text-center">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Process Checkout
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <!-- Interface helper script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const methodSelect = document.getElementById('paymentMethod');
            const helperBank = document.getElementById('helperBank');
            const helperCard = document.getElementById('helperCard');
            const helperMobile = document.getElementById('helperMobile');

            function toggleHelpers() {
                const method = methodSelect.value;
                
                // Hide all
                helperBank.classList.add('hidden');
                helperCard.classList.add('hidden');
                helperMobile.classList.add('hidden');

                // Show selected
                if (method === 'bank_transfer') {
                    helperBank.classList.remove('hidden');
                } else if (method === 'credit_card') {
                    helperCard.classList.remove('hidden');
                } else if (method === 'mobile_payment') {
                    helperMobile.classList.remove('hidden');
                }
            }

            methodSelect.addEventListener('change', toggleHelpers);
            toggleHelpers(); // initial call
        });
    </script>
</x-app-layout>
