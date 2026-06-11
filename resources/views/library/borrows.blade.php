<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Library Loans Registry') }}
            </h2>
            <a href="{{ route('library.borrows.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                Lend Out Book
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

            <!-- Active Borrows Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Book Title</th>
                                <th class="px-6 py-4">Borrower</th>
                                <th class="px-6 py-4">Lend Date</th>
                                <th class="px-6 py-4">Due Date</th>
                                <th class="px-6 py-4">Return Date</th>
                                <th class="px-6 py-4 text-center">Fines Accrued</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                            @forelse($borrows as $borrow)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $borrow->book->title }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-850 dark:text-gray-200">{{ $borrow->user->name }}</td>
                                    <td class="px-6 py-4 text-xs">{{ $borrow->borrow_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-xs">{{ $borrow->due_date->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-450">{{ $borrow->return_date ? $borrow->return_date->format('Y-m-d') : '-' }}</td>
                                    <td class="px-6 py-4 text-center text-rose-500 font-semibold">${{ number_format($borrow->fine_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider
                                            {{ $borrow->status === 'returned' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400' : ($borrow->due_date->isPast() ? 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-400') }}">
                                            {{ $borrow->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($borrow->status === 'borrowed')
                                            <form method="POST" action="{{ route('library.borrows.return', $borrow->id) }}" onsubmit="return confirm('Confirm return of this copy?');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold">
                                                    Process Return
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs">Returned</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No active book loans registered.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-150 dark:border-gray-700">
                    {{ $borrows->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
