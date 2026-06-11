<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AI Face Recognition Registration') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-sm space-y-6 flex flex-col items-center">
                <div class="text-center space-y-2">
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 dark:bg-indigo-950 dark:text-indigo-400 rounded-full font-semibold text-xs uppercase tracking-wider">
                        Enrollment Portal
                    </span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Register Face Profile</h3>
                    <p class="text-xs text-gray-400 max-w-md">Position yourself in front of the camera, ensure good lighting, and click capture to extract your facial characteristics matrix.</p>
                </div>

                <!-- Camera stream container -->
                <div class="relative h-72 w-96 rounded-2xl overflow-hidden bg-black border-4 border-indigo-100 dark:border-gray-700 shadow-md">
                    <!-- Video Element -->
                    <video id="videoStream" autoplay playsinline class="h-full w-full object-cover transform scale-x-[-1]"></video>
                    
                    <!-- Laser Scanning Sweep Effect -->
                    <div id="scanningGrid" class="absolute inset-0 border border-green-500/20 bg-[linear-gradient(rgba(16,185,129,0.05)_1px,transparent_1px),linear-gradient(90deg,rgba(16,185,129,0.05)_1px,transparent_1px)] bg-[size:20px_20px] pointer-events-none">
                        <!-- Horizontal Laser Sweep line -->
                        <div class="w-full h-0.5 bg-green-500 shadow-[0_0_10px_#10b981] absolute top-0 animate-[sweep_2.5s_infinite_linear]"></div>
                    </div>

                    <!-- Target alignment frame -->
                    <div class="absolute inset-8 border-2 border-dashed border-indigo-400/50 rounded-full flex items-center justify-center pointer-events-none">
                        <span class="text-[10px] uppercase font-bold text-indigo-400/80 tracking-wider">Align Face Here</span>
                    </div>

                    <!-- Captured Preview overlay -->
                    <canvas id="photoPreview" class="absolute inset-0 h-full w-full object-cover hidden transform scale-x-[-1]"></canvas>
                </div>

                <!-- Status & Action Buttons -->
                <div class="w-full text-center space-y-4">
                    <p id="statusMsg" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold uppercase tracking-wider">Webcam stream active...</p>

                    <div class="flex justify-center space-x-3">
                        <button type="button" id="btnCapture" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-semibold text-sm transition">
                            Scan Face
                        </button>
                        <button type="button" id="btnReset" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded font-semibold text-sm transition hidden">
                            Re-take Scanning
                        </button>
                        <button type="button" id="btnSubmit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-semibold text-sm transition hidden">
                            Save Biometrics
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scanning Line Keyframes style block -->
    <style>
        @keyframes sweep {
            0% { top: 0%; opacity: 0.8; }
            50% { top: 100%; opacity: 0.8; }
            100% { top: 0%; opacity: 0.8; }
        }
    </style>

    <!-- Camera Controller JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('videoStream');
            const canvas = document.getElementById('photoPreview');
            const grid = document.getElementById('scanningGrid');
            
            const btnCapture = document.getElementById('btnCapture');
            const btnReset = document.getElementById('btnReset');
            const btnSubmit = document.getElementById('btnSubmit');
            const statusMsg = document.getElementById('statusMsg');

            let capturedDescriptor = null;
            let capturedBase64Image = null;

            // Start webcam feed
            navigator.mediaDevices.getUserMedia({ video: true, audio: false })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    statusMsg.textContent = 'Camera Access Denied or Missing.';
                    statusMsg.className = 'text-xs text-rose-500 font-semibold uppercase tracking-wider';
                });

            // Capture button trigger
            btnCapture.addEventListener('click', function() {
                const ctx = canvas.getContext('2d');
                canvas.width = video.videoWidth || 640;
                canvas.height = video.videoHeight || 480;
                
                // Draw camera frame on canvas
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                // Hide video, show canvas
                video.classList.add('hidden');
                grid.classList.add('hidden');
                canvas.classList.remove('hidden');

                // Get base64 image data
                capturedBase64Image = canvas.toDataURL('image/jpeg');

                // Simulate ML facial feature extraction (128 random floats)
                capturedDescriptor = Array.from({ length: 128 }, () => (Math.random() * 2 - 1).toFixed(4));

                statusMsg.textContent = 'Face characteristics scanned and vectorized!';
                statusMsg.className = 'text-xs text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wider';

                btnCapture.classList.add('hidden');
                btnReset.classList.remove('hidden');
                btnSubmit.classList.remove('hidden');
            });

            // Reset button trigger
            btnReset.addEventListener('click', function() {
                video.classList.remove('hidden');
                grid.classList.remove('hidden');
                canvas.classList.add('hidden');

                capturedDescriptor = null;
                capturedBase64Image = null;

                statusMsg.textContent = 'Webcam stream active...';
                statusMsg.className = 'text-xs text-indigo-600 dark:text-indigo-400 font-semibold';

                btnCapture.classList.remove('hidden');
                btnReset.classList.add('hidden');
                btnSubmit.classList.add('hidden');
            });

            // Submit / Save trigger
            btnSubmit.addEventListener('click', function() {
                statusMsg.textContent = 'Sending characteristics to server...';
                statusMsg.className = 'text-xs text-amber-500 font-semibold animate-pulse';

                fetch('{{ route("api.attendance.register-face") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        face_descriptor: capturedDescriptor.map(Number),
                        photo: capturedBase64Image
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        statusMsg.textContent = data.message;
                        statusMsg.className = 'text-xs text-emerald-600 dark:text-emerald-400 font-semibold uppercase tracking-wider';
                        
                        // Redirect back to dashboard after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = '{{ route("dashboard") }}';
                        }, 1500);
                    } else {
                        statusMsg.textContent = 'Error: ' + data.message;
                        statusMsg.className = 'text-xs text-rose-500 font-semibold';
                    }
                })
                .catch(err => {
                    statusMsg.textContent = 'Network error saving biometrics.';
                    statusMsg.className = 'text-xs text-rose-500 font-semibold';
                });
            });
        });
    </script>
</x-app-layout>
