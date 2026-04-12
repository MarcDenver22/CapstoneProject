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

<!-- Include jQuery & face-api -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script async defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
// ==================== JQUERY AJAX SETUP ====================
$.ajaxSetup({
    timeout: 10000,
    dataType: 'json',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
});

let modelsLoaded = false;
const MODEL_PATH = '/storage/models/';

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
let initialCount = {{ auth()->user()->face_samples_count ?? 0 }};

// Get DOM elements
const video = document.getElementById('cameraFeed');
const canvas = document.getElementById('faceCanvas');

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
    console.log('📸 Capture button clicked');
    
    // Ensure camera is ready
    if (!video.srcObject) {
        console.log('⚠️ Camera not ready, initializing...');
        await initCamera();
        await new Promise(resolve => setTimeout(resolve, 1000));
    }

    if (!video.srcObject) {
        alert('❌ Camera is not ready. Please refresh the page and allow camera access.');
        return;
    }

    if (!modelsLoaded) {
        alert('⏳ Face detection models still loading. Please wait...');
        return;
    }

    // Check if video is actually playing
    if (video.paused || video.ended) {
        console.warn('⚠️ Video is not playing, trying to play...');
        try {
            await video.play();
        } catch (e) {
            console.error('❌ Could not play video:', e);
            alert('❌ Video could not be played. Please refresh the page.');
            return;
        }
        await new Promise(resolve => setTimeout(resolve, 500));
    }

    console.log(`📹 Video dimensions: ${video.videoWidth}x${video.videoHeight}`);
    
    if (video.videoWidth === 0 || video.videoHeight === 0) {
        console.error('❌ Video has no dimensions yet, waiting...');
        alert('Video is still loading. Please try again in a moment.');
        return;
    }

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

        // Send descriptor to server
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
                    updateUI(data.sample_count);
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
                    errorMsg = 'Server error (' + xhr.status + '). Response: ' + xhr.responseText;
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
        console.error('❌ Detection error:', error.message, error);
        alert('❌ Error: ' + error.message);
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
                initialCount = 0;
                updateUI(0);
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
    const totalSamples = initialCount + samples.length;
    if (totalSamples < 3) {
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
                window.location.href = data.redirect || '{{ route("employee.dashboard") }}';
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

function updateUI(totalCount = initialCount) {
    const currentCount = initialCount + samples.length;
    sampleCount.textContent = currentCount;
    progressBar.style.width = ((currentCount / 10) * 100) + '%';
    if (currentCount >= 3) completeBtn.disabled = false;

    // Display sample count in grid instead of trying to show images (descriptors aren't images)
    samplesGrid.innerHTML = '';
    for (let i = 0; i < currentCount; i++) {
        const sampleBox = document.createElement('div');
        sampleBox.className = 'w-full h-16 rounded-lg border-2 border-green-500 bg-green-50 flex items-center justify-center';
        sampleBox.innerHTML = `<span class="text-sm font-semibold text-green-700">Sample ${i + 1}</span>`;
        samplesGrid.appendChild(sampleBox);
    }
}

// Only initialize camera when user clicks the button (better UX)
// This avoids automatic permission requests that some browsers block
updateUI();
</script>

@endsection