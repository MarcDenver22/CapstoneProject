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
                            <div id="progressBar" class="h-full bg-blue-600 transition-all duration-300" style="width: {{ ((auth()->user()->face_samples_count ?? 0) / 5) * 100 }}%"></div>
                        </div>
                        <p class="text-gray-700 text-sm mt-2"><span id="sampleCount">{{ auth()->user()->face_samples_count ?? 0 }}</span> / 5 samples</p>
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
        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        video.srcObject = stream;
        video.onloadedmetadata = () => {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
        };
    } catch (err) {
        alert('Unable to access camera: ' + err.message);
    }
}

captureBtn.addEventListener('click', async () => {
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    const imageData = canvas.toDataURL('image/jpeg');
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
        const data = await response.json();
        if (data.success) {
            samples.push(imageData);
            updateUI(data.sample_count);
        }
    } catch (error) {
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
    progressBar.style.width = ((currentCount / 5) * 100) + '%';
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

initCamera();
updateUI();
</script>

@endsection