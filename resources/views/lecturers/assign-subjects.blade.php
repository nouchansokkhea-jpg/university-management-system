<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Assign Course Subjects') }}
            </h2>
            <a href="{{ route('lecturers.show', $lecturer->id) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                Back to Profile
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm space-y-6">
                
                <div>
                    <h3 class="text-lg font-bold text-gray-950 dark:text-white">Mapping: {{ $lecturer->user->name }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Lecturer ID: {{ $lecturer->lecturer_id }} • Department: {{ $lecturer->department->name }}</p>
                </div>

                <form method="POST" action="{{ route('lecturers.assign-subjects', $lecturer->id) }}" class="space-y-6">
                    @csrf

                    <div class="space-y-4">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Subject Catalogue Checklist</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($subjects as $subj)
                                <div class="flex items-start p-4 border rounded-xl dark:border-gray-700 dark:bg-gray-900/30">
                                    <div class="flex items-center h-5">
                                        <input type="checkbox" name="subject_ids[]" value="{{ $subj->id }}" 
                                            {{ $subj->lecturer_id == $lecturer->id ? 'checked' : '' }}
                                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" />
                                    </div>
                                    <div class="ms-3 text-xs">
                                        <label class="font-bold text-gray-900 dark:text-white block">
                                            {{ $subj->subject_code }} - {{ $subj->name }}
                                        </label>
                                        <span class="text-gray-400 block mt-0.5">Course: {{ $subj->course->name }} • {{ $subj->credits }} Credits</span>
                                        @if($subj->lecturer_id && $subj->lecturer_id != $lecturer->id)
                                            <span class="text-rose-500 font-semibold block mt-1">Warning: Currently assigned to {{ $subj->lecturer->user->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 col-span-2 py-4">No unmapped subjects listed in the registry.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end space-x-3 border-t dark:border-gray-700">
                        <a href="{{ route('lecturers.show', $lecturer->id) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Update Subject Mapping
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
