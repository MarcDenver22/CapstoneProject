@extends('layouts.app')

@section('header', 'Face Enrollment')
@section('subheader', 'Register your face for authentication')

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Progress -->
    <div class="flex items-center justify-between mb-10">
        <div class="flex-1 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-green-600 text-white flex items-center justify-center"><i class="fas fa-check"></i></div>
            <p class="text-gray-500 mt-2 text-sm font-semibold">Register</p>
        </div>
        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
        <div class="flex-1 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-blue-600 text-white flex items-center justify-center">2</div>
            <p class="text-gray-700 mt-2 text-sm font-semibold">Face Enrollment</p>
        </div>
    </div>

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
                            <div id="progressBar" class="h-full bg-blue-600 transition-all duration-300" style="width: 0%"></div>
                        </div>
                        <p class="text-gray-700 text-sm mt-2"><span id="sampleCount">0</span> / 10 samples</p>
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

                <button id="completeBtn" type="button" disabled class="w-full mt-4 px-6 py-3 rounded-lg bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold transition">
                    <i class="fas fa-check mr-2"></i> Complete Enrollment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery & face-api -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script async defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
// ==================== JQUERY AJAX SETUP ====================
$.ajaxSetup({
    timeout: 8000,
    dataType: 'json',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
    }
});

let modelsLoaded = false;
const MODEL_PATH = '/storage/models/';

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

// Load face-api models on page load
window.addEventListener('load', async () => {
    if (typeof faceapi === 'undefined') {
        console.error('❌ face-api.js not loaded');
        return;
    }
    
    try {
        console.log('📥 Loading face detection models...');
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_PATH),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_PATH),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_PATH),
        ]);
        modelsLoaded = true;
        console.log('✓ Face detection models loaded successfully');
        
        // Auto-initialize camera when models are loaded
        console.log('📷 Initializing camera...');
        await initCamera();
    } catch (error) {
        console.error('❌ Failed to load models or initialize camera:', error);
    }
});

let samples = [];

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
    console.log('📸 Capture button clicked');
    
    if (!modelsLoaded) {
        alert('⏳ Face detection models still loading. Please wait...');
        return;
    }

    // Check if video is ready
    if (video.paused || video.ended || video.videoWidth === 0) {
        console.warn('⚠️ Video not ready, waiting...');
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    if (video.videoWidth === 0 || video.videoHeight === 0) {
        alert('⚠️ Video dimensions not available. Please refresh the page.');
        return;
    }

    console.log(`📹 Video dimensions: ${video.videoWidth}x${video.videoHeight}`);
    console.log('🔍 Starting face detection...');
    
    captureIndicator.classList.remove('hidden');
    captureBtn.disabled = true;

    try {
        console.log('🔍 Detecting face in video...');
        
        // Add timeout wrapper
        const detectionPromise = faceapi
            .detectSingleFace(video)
            .withFaceLandmarks()
            .withFaceDescriptor();
        
        const timeoutPromise = new Promise((_, reject) => 
            setTimeout(() => reject(new Error('Face detection timed out after 10 seconds')), 10000)
        );
        
        const detection = await Promise.race([detectionPromise, timeoutPromise]);

        if (!detection || !detection.descriptor) {
            console.warn('⚠️ No face detected in frame');
            alert('⚠️ No face detected. Ensure:\n✓ Good lighting\n✓ Face is 30-60cm from camera\n✓ Face is centered in frame');
            captureIndicator.classList.add('hidden');
            captureBtn.disabled = false;
            return;
        }

        console.log('✓ Face detected, descriptor extracted');
        console.log('  Detection confidence:', detection.detection.score);
        // Convert Float32Array to regular array for JSON serialization
        const descriptorArray = Array.from(detection.descriptor);
        console.log('  Descriptor length:', descriptorArray.length);
        console.log('📤 Sending descriptor to server...');

        $.ajax({
            url: '{{ route("employee.face.save_sample") }}',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ face_descriptor: descriptorArray }),
            timeout: 15000,
            success: function(data) {
                console.log('✓ Server response:', data);
                if (data.success) {
                    samples.push(descriptorArray);
                    updateUI();
                    console.log('✓ Sample ' + data.sample_count + ' saved successfully');
                    alert('✓ Sample ' + data.sample_count + ' captured! ' + (10 - data.sample_count) + ' more to go.');
                } else {
                    console.error('❌ Server error:', data.message);
                    alert('❌ Error: ' + (data.message || 'Failed to save sample'));
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = error;
                if (status === 'timeout') {
                    errorMsg = 'Request timeout. Please try again.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status >= 400) {
                    errorMsg = 'Server error (' + xhr.status + ')';
                }
                console.error('❌ AJAX Error:', status, ':', errorMsg);
                alert('❌ Error: ' + errorMsg);
            },
            complete: function() {
                captureIndicator.classList.add('hidden');
                captureBtn.disabled = false;
            }
        });
    } catch (error) {
        console.error('❌ Detection error:', error.message);
        alert('❌ Error: ' + error.message);
        console.error('Detection error:', error);
        alert('Error: ' + error.message);
        captureIndicator.classList.add('hidden');
        captureBtn.disabled = false;
    }
});

resetBtn.addEventListener('click', () => {
    if (confirm('Reset all samples?')) {
        $.ajax({
            url: '{{ route("employee.face.reset") }}',
            type: 'POST',
            timeout: 8000,
            success: function(data) {
                samples = [];
                updateUI();
            },
            error: function(xhr, status, error) {
                let errorMsg = error;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alert('Error: ' + errorMsg);
            }
        });
    }
});

completeBtn.addEventListener('click', () => {
    if (samples.length < 3) {
        alert('Please capture at least 3 samples');
        return;
    }

    $.ajax({
        url: '{{ route("employee.face.complete") }}',
        type: 'POST',
        contentType: 'application/json',
        timeout: 8000,
        success: function(data) {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Error: ' + (data.message || 'Failed to complete enrollment'));
            }
        },
        error: function(xhr, status, error) {
            let errorMsg = error;
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            alert('Error: ' + errorMsg);
        }
    });
});

function updateUI() {
    sampleCount.textContent = samples.length;
    progressBar.style.width = ((samples.length / 10) * 100) + '%';
    if (samples.length >= 3) completeBtn.disabled = false;

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
</script>

@endsection