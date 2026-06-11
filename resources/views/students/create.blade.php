<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Register New Student') }}
            </h2>
            <a href="{{ route('students.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm space-y-6">

                @if ($errors->any())
                    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-950 dark:text-rose-300 rounded">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <h3 class="text-md font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b pb-2 dark:border-gray-700">Account Credentials</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                            <input type="password" name="password" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                    </div>

                    <h3 class="text-md font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b pb-2 dark:border-gray-700 mt-8">Student Personal Details</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Gender</label>
                            <select name="gender" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                                <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Date of Birth</label>
                            <input type="date" name="dob" value="{{ old('dob') }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Home Address</label>
                        <textarea name="address" required rows="2" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">{{ old('address') }}</textarea>
                    </div>

                    <h3 class="text-md font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b pb-2 dark:border-gray-700 mt-8">Academic Enrollment</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Department Assignment</label>
                            <select name="department_id" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }} ({{ $dept->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Enrollment Date</label>
                            <input type="date" name="enrollment_date" value="{{ old('enrollment_date', date('Y-m-d')) }}" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                            <select name="status" required class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h3 class="text-md font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b pb-2 dark:border-gray-700 mt-8">Pre-Academic History</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">High School / Prior Institution</label>
                            <input type="text" name="high_school" value="{{ old('high_school') }}" placeholder="e.g. Oakridge Academy" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Prior GPA</label>
                            <input type="text" name="high_school_gpa" value="{{ old('high_school_gpa') }}" placeholder="e.g. 3.85" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Profile Photo upload</label>
                        <input type="file" id="photoInput" name="photo" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-gray-900 dark:file:text-indigo-400" />
                        <p id="photoError" class="text-xs text-rose-600 dark:text-rose-400 mt-1 hidden font-semibold"></p>
                        <p class="text-[10px] text-gray-400 mt-1">Maximum file size: 2MB (PHP Limit). Allowed formats: JPG, PNG, GIF, WebP.</p>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const photoInput = document.getElementById('photoInput');
                            const photoError = document.getElementById('photoError');
                            const submitBtn = document.querySelector('button[type="submit"]');

                            if (photoInput) {
                                photoInput.addEventListener('change', function(e) {
                                    const file = e.target.files[0];
                                    if (file) {
                                        const maxSize = 2 * 1024 * 1024; // 2MB
                                        if (file.size > maxSize) {
                                            photoError.textContent = 'Selected image is too large (' + (file.size / (1024 * 1024)).toFixed(2) + 'MB). It must be less than 2MB due to PHP configuration upload limits.';
                                            photoError.classList.remove('hidden');
                                            e.target.value = ''; // clear input
                                            if (submitBtn) submitBtn.disabled = true;
                                        } else {
                                            photoError.classList.add('hidden');
                                            if (submitBtn) submitBtn.disabled = false;
                                        }
                                    } else {
                                        photoError.classList.add('hidden');
                                        if (submitBtn) submitBtn.disabled = false;
                                    }
                                });
                            }
                        });
                    </script>

                    <div class="pt-6 flex justify-end space-x-3 border-t dark:border-gray-700">
                        <a href="{{ route('students.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Save Student Record
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
