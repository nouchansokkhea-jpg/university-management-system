<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Disburse Employee Salary') }}
            </h2>
            <a href="{{ route('hr.payroll.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                Back to Payroll
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm space-y-6">

                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('hr.payroll.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Employee</label>
                            <select name="user_id" id="userId" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                                <option value="">-- Choose Staff/Lecturer --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp['id'] }}" data-salary="{{ $emp['salary'] }}" {{ old('user_id') == $emp['id'] ? 'selected' : '' }}>
                                        {{ $emp['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Disbursement Period</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="month" required class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-xs focus:ring-indigo-500">
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                                            {{ date('M', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endfor
                                </select>
                                <select name="year" required class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-xs focus:ring-indigo-500">
                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                    <option value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-md font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b pb-2 dark:border-gray-700 mt-6">Financial parameters</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Basic Base Salary ($)</label>
                            <input type="number" step="0.01" name="basic_salary" id="basicSalary" value="{{ old('basic_salary', 0.00) }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Allowances ($)</label>
                            <input type="number" step="0.01" name="allowances" value="{{ old('allowances', 0.00) }}" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Deductions ($)</label>
                            <input type="number" step="0.01" name="deductions" value="{{ old('deductions', 0.00) }}" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Disbursement status</label>
                        <select name="status" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                            <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Paid & Disbursed</option>
                            <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                        </select>
                    </div>

                    <div class="pt-6 flex justify-end space-x-3 border-t dark:border-gray-700">
                        <a href="{{ route('hr.payroll.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Process Payroll
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Auto-fill salary script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userSelect = document.getElementById('userId');
            const salaryInput = document.getElementById('basicSalary');

            userSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const salary = selectedOption.getAttribute('data-salary');
                if (salary) {
                    salaryInput.value = salary;
                } else {
                    salaryInput.value = '0.00';
                }
            });
        });
    </script>
</x-app-layout>
