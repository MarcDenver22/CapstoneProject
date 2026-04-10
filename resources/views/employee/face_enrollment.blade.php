@extends('layouts.app')

@section('header', 'Face Enrollment')
@section('subheader', 'Register your face for authentication')

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                <h3 class="text-gray-800 font-bold text-lg mb-4">Your Details</h3>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="text-gray-500 uppercase text-xs font-semibold">Name</p>
                        <p class="text-gray-800 font-semibold">{{ auth()->user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 uppercase text-xs font-semibold">Email</p>
                        <p class="text-gray-800 font-semibold">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 uppercase text-xs font-semibold">Position</p>
                        <p class="text-gray-800 font-semibold">{{ auth()->user()->position ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 uppercase text-xs font-semibold">Department</p>
                        <p class="text-gray-800 font-semibold">{{ auth()->user()->department?->name ?? 'N/A' }}</p>
                    </div>
                    <hr class="my-4 border-gray-200">
                    <div>
                        <p class="text-gray-500 text-xs uppercase font-semibold mb-2">Samples Captured</p>
                        <div class="relative h-2 rounded-full bg-gray-200 overflow-hidden">
                            <div id="progressBar" class="h-full bg-blue-600 transition-all duration-300" style="width: {{ ((auth()->user()->face_samples_count ?? 0) / 10) * 100 }}%"></div>
                        </div>
                        <p class="text-gray-700 text-sm mt-2"><span id="sampleCount">{{ auth()->user()->face_samples_count ?? 0 }}</span> / 10 samples</p>
                        <p class="text-gray-500 text-xs mt-1">Minimum 3 required</p>
                    </div>
                    <div class="mt-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
                        <h4 class="text-blue-700 font-semibold text-sm mb-2">Tips:</h4>
                        <ul class="text-blue-600 text-xs space-y-1">
                            <li>✓ 30-60cm away</li>
                            <li>✓ Good lighting</li>
                            <li>✓ Different angles</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="relative w-full rounded-lg overflow-hidden bg-black mb-6" style="aspect-ratio: 16/12;">
                    <video id="cameraFeed" autoplay playsinline class="w-full h-full object-cover"></video>
                    <canvas id="faceCanvas" class="hidden"></canvas>
                    <div id="captureIndicator" class="absolute top-4 right-4 px-4 py-2 rounded-full bg-red-100 border border-red-300 hidden">
                        <span class="text-red-600 text-sm font-semibold flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span> Capturing...</span>
                    </div>
                </div>

                <div class="flex gap-4 mb-6">
                    <button id="captureBtn" class="flex-1 px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                        <i class="fas fa-camera mr-2"></i> Capture Sample
                    </button>
                    <button id="resetBtn" class="px-6 py-3 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold transition">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>

                <div>
                    <h4 class="text-gray-800 font-semibold mb-4">Captured Samples</h4>
                    <div id="samplesGrid" class="grid grid-cols-3 md:grid-cols-5 gap-4 mb-6"></div>
                </div>

                <button id="completeBtn" type="button" {{ auth()->user()->face_samples_count >= 3 ? '' : 'disabled' }} class="w-full mt-4 px-6 py-3 rounded-lg bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold transition">
                    <i class="fas fa-check mr-2"></i> Complete Enrollment
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const video = document.getElementById('cameraFeed');
const canvas = document.getElementById('faceCanvas');
const ctx = canvas.getContext('2d');
const captureBtn = document.getElementById('captureBtn');
const resetBtn = document.getElementById('resetBtn');
const completeBtn = document.getElementById('completeBtn');
const samplesGrid = document.getElementById('samplesGrid');
const progressBar = document.getElementById('progressBar');
const sampleCount = document.getElementById('sampleCount');
const captureIndicator = document.getElementById('captureIndicator');

let samples = [];
let initialCount = {{ auth()->user()->face_samples_count ?? 0 }};

async function initCamera() {
    try {
        // Check if browser supports getUserMedia
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Your browser does not support camera access. Please use Chrome, Firefox, Safari, or Edge.');
        }

        const constraints = {
            video: {
                facingMode: 'user',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        };

        const stream = await navigator.mediaDevices.getUserMedia(constraints);
        video.srcObject = stream;
        
        video.onloadedmetadata = () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            console.log('Camera ready:', video.videoWidth, 'x', video.videoHeight);
        };

        // Ensure video plays
        video.play().catch(err => {
            console.error('Video play error:', err);
            alert('Unable to play video stream. Please refresh the page.');
        });

    } catch (err) {
        console.error('Camera error:', err);
        let message = 'Unable to access camera: ' + err.message;
        
        if (err.name === 'NotAllowedError') {
            message = 'Camera permission denied. Please allow camera access in your browser settings and refresh the page.';
        } else if (err.name === 'NotFoundError') {
            message = 'No camera device found. Please make sure your device has a camera.';
        } else if (err.name === 'NotReadableError') {
            message = 'Camera is already in use by another app. Please close other camera apps.';
        }
        
        alert(message);
        captureBtn.disabled = true;
    }
}

captureBtn.addEventListener('click', async () => {
    // If camera not initialized, try initializing first
    if (!video.srcObject) {
        console.log('Camera not ready, initializing...');
        await initCamera();
        // Wait a moment for camera to initialize
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    // Check if we have a video stream
    if (!video.srcObject) {
        alert('Camera is not ready. Please refresh the page and allow camera access.');
        return;
    }

    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const imageData = canvas.toDataURL('image/jpeg', 0.9);
    captureIndicator.classList.remove('hidden');
    captureBtn.disabled = true;

    try {
        const response = await fetch('{{ route("employee.face.save_sample") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ face_sample: imageData })
        });

        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const textResponse = await response.text();
            console.error('Server returned non-JSON:', textResponse.substring(0, 200));
            throw new Error('Server error: ' + response.statusText + ' (check console for details)');
        }

        const data = await response.json();
        
        if (!response.ok) {
            console.error('API error:', data);
            throw new Error(data.message || 'Failed to save sample');
        }

        if (data.success) {
            samples.push(imageData);
            updateUI(data.sample_count);
            console.log('Sample saved successfully');
        } else {
            alert('Error: ' + (data.message || 'Failed to save sample'));
        }
    } catch (error) {
        console.error('Capture error:', error);
        alert('Error: ' + error.message);
    } finally {
        captureIndicator.classList.add('hidden');
        captureBtn.disabled = false;
    }
});

resetBtn.addEventListener('click', async () => {
    if (confirm('Reset all samples?')) {
        try {
            await fetch('{{ route("employee.face.reset") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            samples = [];
            initialCount = 0;
            updateUI(0);
        } catch (error) {
            alert('Error: ' + error.message);
        }
    }
});

completeBtn.addEventListener('click', async () => {
    const totalSamples = initialCount + samples.length;
    if (totalSamples < 3) {
        alert('Please capture at least 3 samples');
        return;
    }

    try {
        const response = await fetch('{{ route("employee.face.complete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        const data = await response.json();
        if (data.success) {
            window.location.href = data.redirect || '{{ route("employee.dashboard") }}';
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
});

function updateUI(totalCount = initialCount) {
    const currentCount = initialCount + samples.length;
    sampleCount.textContent = currentCount;
    progressBar.style.width = ((currentCount / 10) * 100) + '%';
    if (currentCount >= 3) completeBtn.disabled = false;

    samplesGrid.innerHTML = '';
    samples.forEach((sample, index) => {
        const img = document.createElement('img');
        img.src = sample;
        img.className = 'w-full h-32 object-cover rounded-lg border-2 border-green-500 cursor-pointer hover:opacity-80';
        img.onclick = () => {
            samples.splice(index, 1);
            updateUI();
        };
        samplesGrid.appendChild(img);
    });
}

// Only initialize camera when user clicks the button (better UX)
// This avoids automatic permission requests that some browsers block
updateUI();
</script>

@endsection