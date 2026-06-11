<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dynamic Classroom QR Code Generator') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Selector -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                <form method="GET" action="{{ route('attendance.qr-generator') }}" id="qrSelectorForm">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Active Subject Lecture</label>
                        <select name="subject_id" onchange="this.form.submit()" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm focus:ring-indigo-500">
                            <option value="">-- Choose Course Subject --</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}" {{ request('subject_id') == $subj->id ? 'selected' : '' }}>
                                    {{ $subj->subject_code }} - {{ $subj->name }} ({{ $subj->course->course_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- QR Code Display -->
            @if($selectedSubject && $qrPayload)
                <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm space-y-6 flex flex-col items-center text-center">
                    <div>
                        <span class="px-3 py-1 bg-rose-50 text-rose-600 dark:bg-rose-950 dark:text-rose-400 rounded-full font-semibold text-xs uppercase tracking-wider">
                            Anti-Spoofing Active
                        </span>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-3">{{ $selectedSubject->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Classroom Check-In QR Code for {{ date('M d, Y') }}</p>
                    </div>

                    <!-- Dynamic QR image -->
                    <div class="p-4 bg-white rounded-2xl shadow border-4 border-indigo-100 flex items-center justify-center">
                        <img id="qrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($qrPayload) }}" alt="Check-in QR Code" class="h-60 w-60" />
                    </div>

                    <!-- Timer indicator -->
                    <div class="space-y-1 w-full max-w-xs">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Refreshing in</span>
                            <span class="font-semibold text-gray-800 dark:text-white" id="countdown">30s</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full rounded-full transition-all duration-1000 ease-linear" id="progressBar" style="width: 100%"></div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 max-w-sm">Students must open the Apex UMS Mobile Portal, click "Scan QR Code", and hover over this screen. Coordinates and timestamp will be matched.</p>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        let duration = 30; // 30 seconds between refreshes
                        let remaining = duration;
                        const countdownEl = document.getElementById('countdown');
                        const barEl = document.getElementById('progressBar');

                        const timer = setInterval(() => {
                            remaining--;
                            countdownEl.textContent = remaining + 's';
                            
                            // Adjust progress bar
                            const percent = (remaining / duration) * 100;
                            barEl.style.width = percent + '%';

                            if (remaining <= 0) {
                                clearInterval(timer);
                                // Reload page to fetch new payload
                                window.location.reload();
                            }
                        }, 1000);
                    });
                </script>
            @endif

        </div>
    </div>
</x-app-layout>
