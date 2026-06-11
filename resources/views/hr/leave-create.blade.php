<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('File Leave Application') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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

                <form method="POST" action="{{ route('hr.leaves.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Leave Type Category</label>
                        <select name="leave_type" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                            <option value="sick" {{ old('leave_type') === 'sick' ? 'selected' : '' }}>Sick Leave (Medical reasons)</option>
                            <option value="casual" {{ old('leave_type') === 'casual' ? 'selected' : '' }}>Casual Leave (Personal events)</option>
                            <option value="annual" {{ old('leave_type') === 'annual' ? 'selected' : '' }}>Annual Paid Vacation Leave</option>
                            <option value="maternity" {{ old('leave_type') === 'maternity' ? 'selected' : '' }}>Maternity / Paternity Leave</option>
                            <option value="unpaid" {{ old('leave_type') === 'unpaid' ? 'selected' : '' }}>Unpaid Leave of Absence</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Reason / Details</label>
                        <textarea name="reason" rows="4" required placeholder="Detail the reasons for leave and handover preparations..." class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">{{ old('reason') }}</textarea>
                    </div>

                    <div class="pt-6 flex justify-end space-x-3 border-t dark:border-gray-700">
                        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition text-center">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Submit Application
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
