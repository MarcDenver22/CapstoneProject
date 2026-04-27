@extends('layouts.app')

@section('title', 'Face Enrollment')
@section('header', 'Face Enrollment')
@section('subheader', 'Register your face for authentication')

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Enhanced Progress Section -->
    <div class="mb-12 px-4">
        <!-- Calculate Progress -->
        @php
            $samplesCount = auth()->user()->face_samples_count ?? 0;
            $progressPercent = min(100, ($samplesCount / 3) * 100);
            $isComplete = $samplesCount >= 3;
        @endphp

        <!-- Progress Percentage -->
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">Enrollment Progress</h3>
            <span class="text-3xl font-bold {{ $isComplete ? 'text-green-600' : 'text-blue-600' }}">{{ round($progressPercent) }}%</span>
        </div>

        <!-- Progress Bar -->
        <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden mb-8">
            <div class="h-full bg-gradient-to-r {{ $isComplete ? 'from-green-500 to-green-600' : 'from-blue-500 to-blue-600' }} transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
        </div>

        <!-- Steps Container -->
        <div class="grid grid-cols-2 gap-8">
            <!-- Step 1: Register -->
            <div class="relative">
                <div class="flex items-start gap-4">
                    <!-- Circle Badge -->
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg ring-4 ring-green-100">
                            <i class="fas fa-check text-white text-2xl"></i>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="flex-1 pt-2">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-lg font-bold text-gray-800">Step 1: Register</h4>
                            <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">Complete</span>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">Complete your profile information and create your account</p>
                        <div class="flex items-center gap-2 text-green-600 text-sm font-medium">
                            <i class="fas fa-check-circle"></i>
                            <span>Profile saved successfully</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Face Enrollment -->
            <div class="relative">
                <div class="flex items-start gap-4">
                    <!-- Circle Badge -->
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg ring-4 ring-blue-100 animate-pulse">
                            <span class="text-white text-2xl font-bold">2</span>
                        </div>
                    </div>
                    <!-- Content -->
                    <div class="flex-1 pt-2">
                        <div class="flex items-center gap-3 mb-2">
                            <h4 class="text-lg font-bold text-gray-800">Step 2: Face Enrollment</h4>
                            <span class="inline-block {{ $isComplete ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }} text-xs font-semibold px-3 py-1 rounded-full">
                                {{ $isComplete ? '✓ Complete' : 'Current' }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">Capture 3-10 face samples for facial recognition authentication</p>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fas fa-clock {{ $isComplete ? 'text-green-500' : 'text-blue-500' }}"></i>
                                <span><strong>2-3 minutes</strong></span>
                            </div>
                            <div class="flex items-center gap-2 {{ $isComplete ? 'text-green-700' : 'text-gray-700' }}">
                                <i class="fas fa-camera {{ $isComplete ? 'text-green-500' : 'text-blue-500' }}"></i>
                                <span><strong>{{ $samplesCount }}/3 samples</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                        <p class="text-gray-500 uppercase text-xs font-semibold">Employee ID</p>
                        <p class="text-gray-800 font-semibold">{{ auth()->user()->faculty_id ?? 'N/A' }}</p>
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
<!-- Include jQuery & face-api -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script async defer src="/js/face-api.min.js"></script>

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
const statusMessage = document.getElementById('statusMessage');

// ===== STATUS MESSAGE UPDATER =====
function updateStatus(message, icon = 'circle', type = 'info') {
    const iconClass = {
        'check': 'fas fa-check-circle text-green-500',
        'warning': 'fas fa-exclamation-circle text-yellow-500',
        'error': 'fas fa-times-circle text-red-500',
        'loading': 'fas fa-spinner text-blue-500 animate-spin',
        'circle': 'fas fa-circle text-gray-400',
        'camera': 'fas fa-camera text-blue-500',
        'face': 'fas fa-face-smile text-blue-500'
    }[icon] || 'fas fa-circle text-gray-400';
    
    if (statusMessage) {
        statusMessage.innerHTML = `<p><i class="${iconClass} text-xs mr-2"></i>${message}</p>`;
    }
    console.log(`📊 Status: ${message}`);
}

// Load face-api models on page load
window.addEventListener('load', async () => {
    if (typeof faceapi === 'undefined') {
        console.error('❌ face-api.js not loaded');
        updateStatus('Error: Face detection library failed to load', 'error', 'error');
        return;
    }
    
    try {
        updateStatus('Loading face detection models...', 'loading', 'loading');
        console.log('📥 Loading face detection models...');
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_PATH),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_PATH),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_PATH),
        ]);
        modelsLoaded = true;
        updateStatus('✓ Models loaded successfully. Initializing camera...', 'check', 'success');
        console.log('✓ Face detection models loaded successfully');
        
        // Auto-initialize camera when models are loaded
        console.log('📷 Initializing camera...');
        await initCamera();
        updateStatus('✓ Camera ready. Click "Capture Sample" to begin', 'camera', 'success');
    } catch (error) {
        updateStatus('Error: Failed to initialize. Please refresh the page.', 'error', 'error');
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
    updateStatus('Scanning for face...', 'loading', 'loading');
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
            updateStatus('No face detected. Adjust lighting or position closer to camera', 'warning', 'warning');
            alert('⚠️ No face detected. Ensure:\n✓ Good lighting\n✓ Face is 30-60cm from camera\n✓ Face is centered in frame');
            captureIndicator.classList.add('hidden');
            captureBtn.disabled = false;
            return;
        }

        console.log('✓ Face detected, descriptor extracted');
        updateStatus('✓ Face detected! Uploading sample...', 'face', 'success');
        console.log('  Detection confidence:', detection.detection.score);
        // Convert Float32Array to regular array for JSON serialization
        const descriptorArray = Array.from(detection.descriptor);
        console.log('  Descriptor length:', descriptorArray.length);
        console.log('📤 Sending descriptor to server...');

        // Send descriptor to server
        $.ajax({
            url: '{{ route("employee.face_enrollment.save_sample") }}',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ face_descriptor: descriptorArray }),
            timeout: 15000,
            success: function(data) {
                console.log('✓ Server response:', data);
                if (data.success) {
                    samples.push(descriptorArray);
                    updateUI(data.sample_count);
                    const remaining = Math.max(0, 3 - data.sample_count);
                    
                    if (remaining === 0) {
                        updateStatus('✓ Sample ' + data.sample_count + ' captured! All required samples complete. Click "Complete Enrollment" to finish.', 'check', 'success');
                    } else {
                        updateStatus('✓ Sample ' + data.sample_count + ' captured! ' + remaining + ' more sample(s) needed.', 'check', 'success');
                    }
                    
                    console.log('✓ Sample ' + data.sample_count + ' saved successfully');
                    alert('✓ Sample ' + data.sample_count + ' captured! ' + (10 - data.sample_count) + ' more to go.');
                } else {
                    console.error('❌ Server error:', data.message);
                    updateStatus('Error saving sample: ' + (data.message || 'Unknown error'), 'error', 'error');
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
                updateStatus('Error: ' + errorMsg, 'error', 'error');
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
            url: '{{ route("employee.face_enrollment.reset") }}',
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
        url: '{{ route("employee.face_enrollment.complete") }}',
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
    const totalSamples = currentCount;
    
    // Update counters
    sampleCount.textContent = totalSamples;
    progressBar.style.width = ((totalSamples / 10) * 100) + '%';
    
    // Enable complete button if we have at least 3 samples
    if (totalSamples >= 3) {
        completeBtn.disabled = false;
    } else {
        completeBtn.disabled = true;
    }

    // Update the samples grid display
    samplesGrid.innerHTML = '';
    
    // Display samples from database (initialCount)
    for (let i = 0; i < initialCount; i++) {
        const sampleBox = document.createElement('div');
        sampleBox.className = 'w-full h-20 rounded-lg border-2 border-blue-500 bg-blue-50 flex flex-col items-center justify-center relative group';
        sampleBox.innerHTML = `
            <span class="text-sm font-semibold text-blue-700">Sample ${i + 1}</span>
            <span class="text-xs text-blue-600 mt-1">(Saved)</span>
        `;
        samplesGrid.appendChild(sampleBox);
    }
    
    // Display newly captured samples in this session
    for (let i = 0; i < samples.length; i++) {
        const sampleBox = document.createElement('div');
        sampleBox.className = 'w-full h-20 rounded-lg border-2 border-green-500 bg-green-50 flex flex-col items-center justify-center relative group';
        
        // Add delete button for newly captured samples
        const deleteBtn = `
            <button type="button" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs font-bold transition opacity-0 group-hover:opacity-100 delete-sample" data-index="${i}">
                ×
            </button>
        `;
        
        sampleBox.innerHTML = `
            ${deleteBtn}
            <span class="text-sm font-semibold text-green-700">Sample ${initialCount + i + 1}</span>
            <span class="text-xs text-green-600 mt-1">(New)</span>
        `;
        samplesGrid.appendChild(sampleBox);
        
        // Attach delete handler for newly captured samples
        const dialog = sampleBox.querySelector('.delete-sample');
        dialog.addEventListener('click', (e) => {
            e.preventDefault();
            const sampleIndex = parseInt(dialog.dataset.index);
            deleteSample(sampleIndex);
        });
    }
    
    console.log(`UI Updated: ${initialCount} saved + ${samples.length} new = ${totalSamples} total`);
}

function deleteSample(index) {
    if (confirm('Delete this sample?')) {
        // Remove from local array (only for newly captured samples)
        samples.splice(index, 1);
        
        // Reset all samples on server
        $.ajax({
            url: '{{ route("employee.face_enrollment.reset") }}',
            type: 'POST',
            contentType: 'application/json',
            timeout: 8000,
            success: function(data) {
                console.log('✓ All samples reset on server');
                
                // Re-save all remaining newly captured samples in order
                if (samples.length > 0) {
                    let savedCount = 0;
                    const samplesToSave = [...samples]; // Create a copy
                    
                    samplesToSave.forEach((descriptor, idx) => {
                        $.ajax({
                            url: '{{ route("employee.face_enrollment.save_sample") }}',
                            type: 'POST',
                            contentType: 'application/json',
                            data: JSON.stringify({ face_descriptor: descriptor }),
                            timeout: 15000,
                            success: function(data) {
                                savedCount++;
                                console.log(`✓ Sample resaved (${savedCount}/${samplesToSave.length})`);
                                
                                // Only update when all are done
                                if (savedCount === samplesToSave.length) {
                                    initialCount = data.sample_count - samplesToSave.length + samplesToSave.length;
                                    console.log('Sample count synced. Total:', initialCount + samples.length);
                                    updateUI();
                                    console.log('✓ All remaining samples resaved and synced');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('❌ Error resaving sample:', error);
                                alert('Error resaving samples. Please refresh the page.');
                                location.reload();
                            }
                        });
                    });
                } else {
                    // No samples to resave, just reset counts
                    initialCount = 0;
                    updateUI();
                    console.log('✓ Sample deleted successfully');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error resetting samples:', error);
                alert('Error deleting sample. Please refresh the page.');
                location.reload();
            }
        });
    }
}

// Only initialize camera when user clicks the button (better UX)
// This avoids automatic permission requests that some browsers block
updateUI();
</script>

@endsection