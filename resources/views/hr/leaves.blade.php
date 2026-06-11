<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Faculty & Staff Leave Requests') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Leaves Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Employee Name</th>
                                <th class="px-6 py-4">Leave Type</th>
                                <th class="px-6 py-4">Period</th>
                                <th class="px-6 py-4">Reason</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Approver</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                            @forelse($leaves as $leave)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $leave->user->name }}</td>
                                    <td class="px-6 py-4 font-semibold uppercase text-xs">{{ $leave->leave_type }}</td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ $leave->start_date->format('Y-m-d') }} to {{ $leave->end_date->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 max-w-[200px] truncate text-xs" title="{{ $leave->reason }}">{{ $leave->reason }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                                            {{ $leave->status === 'approved' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : ($leave->status === 'rejected' ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400') }}">
                                            {{ $leave->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400">{{ $leave->approver?->name ?? 'Pending Review' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($leave->status === 'pending')
                                            <div class="flex items-center justify-center space-x-2">
                                                <form method="POST" action="{{ route('hr.leaves.approve', $leave->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved" />
                                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                                        Approve
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('hr.leaves.approve', $leave->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected" />
                                                    <button type="submit" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-semibold">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No leave requests logged in system.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-150 dark:border-gray-700">
                    {{ $leaves->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
