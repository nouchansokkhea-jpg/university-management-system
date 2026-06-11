<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Library Catalog Search') }}
            </h2>
            @if(Auth::user()->hasRole(['super_admin', 'registrar']))
                <a href="{{ route('library.books.create') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                    + Catalog New Book
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search bar -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <form method="GET" action="{{ route('library.books.index') }}" class="flex space-x-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, author, category, or ISBN..." class="flex-1 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                        Search Catalog
                    </button>
                    <a href="{{ route('library.books.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition flex items-center justify-center">
                        Reset
                    </a>
                </form>
            </div>

            <!-- Book list table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-900 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-4">Book Title</th>
                                <th class="px-6 py-4">Author</th>
                                <th class="px-6 py-4">ISBN</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4 text-center">Available / Total</th>
                                <th class="px-6 py-4">Shelf Location</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 dark:divide-gray-700">
                            @forelse($books as $book)
                                <tr>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">{{ $book->title }}</td>
                                    <td class="px-6 py-4 text-gray-700 dark:text-gray-300">{{ $book->author }}</td>
                                    <td class="px-6 py-4 text-xs font-mono">{{ $book->isbn ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-xs">{{ $book->category ?? 'General' }}</td>
                                    <td class="px-6 py-4 text-center">{{ $book->available_copies }} / {{ $book->total_copies }}</td>
                                    <td class="px-6 py-4 text-xs">{{ $book->location_shelf ?? 'General Stacks' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($book->available_copies > 0)
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400">
                                                Available
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400">
                                                On Loan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">No books found matching search parameters.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-gray-50 dark:bg-gray-900 border-t border-gray-150 dark:border-gray-700">
                    {{ $books->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
