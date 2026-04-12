/**
 * Face Enrollment System - Capture Face Samples for Training
 * 
 * This module handles initial face enrollment where users capture
 * 3-10 face samples that are used to train the face recognition system.
 * 
 * Dependencies: face-api.js, jQuery
 * Models Location: /public/models
 */

// ==================== JQUERY AJAX SETUP ====================
$.ajaxSetup({
    timeout: 8000,
    dataType: 'json',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfToken()
    },
    error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
    }
});

// Global state
let videoStream = null;
let modelsLoaded = false;
let sampleCount = 0;
let isEnrolling = false;

// Configuration
const ENROLLMENT_CONFIG = {
    MIN_SAMPLES: 3,
    MAX_SAMPLES: 10,
    DETECTION_TIMEOUT: 10000,  // Increased from 5000 to give more time for detection
    modelPath: '/storage/models/'
};

// ==================== INITIALIZATION ====================

/**
 * Initialize enrollment system on page load
 */
document.addEventListener('DOMContentLoaded', async () => {
    console.log('🚀 Face Enrollment System initializing...');
    showMessage('Loading AI models...', 'info');

    try {
        if (typeof faceapi === 'undefined') {
            throw new Error('face-api.js library not loaded');
        }

        // Load models
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(ENROLLMENT_CONFIG.modelPath),
            faceapi.nets.faceRecognitionNet.loadFromUri(ENROLLMENT_CONFIG.modelPath),
            faceapi.nets.faceLandmark68Net.loadFromUri(ENROLLMENT_CONFIG.modelPath),
        ]);

        modelsLoaded = true;
        console.log('✅ All models loaded');
        showMessage('✓ Models loaded successfully. Click "Start Camera" to begin.', 'success');

        // Check existing enrollment status (non-blocking AJAX)
        checkEnrollmentStatus();

    } catch (error) {
        console.error('❌ Model loading failed:', error);
        showMessage(`❌ Failed to load models: ${error.message}`, 'error');
        document.getElementById('startButton').disabled = true;
    }

    attachEventListeners();
});

/**
 * Attach event listeners to buttons
 */
function attachEventListeners() {
    const startBtn = document.getElementById('startButton');
    const captureBtn = document.getElementById('captureButton');
    const completeBtn = document.getElementById('completeButton');
    const resetBtn = document.getElementById('resetButton');

    if (startBtn) startBtn.addEventListener('click', startCamera);
    if (captureBtn) captureBtn.addEventListener('click', captureFace);
    if (completeBtn) completeBtn.addEventListener('click', completeEnrollment);
    if (resetBtn) resetBtn.addEventListener('click', resetEnrollment);

    console.log('✓ Event listeners attached');
}

// ==================== STATUS CHECK ====================

/**
 * Check existing enrollment status from server
 */
function checkEnrollmentStatus() {
    $.ajax({
        url: '{{ route("face-enrollment.status") }}',
        type: 'GET',
        timeout: 5000,
        success: function(data) {
            sampleCount = data.sample_count || 0;

            if (data.face_enrolled) {
                showMessage('✓ You have already completed face enrollment!', 'success');
                document.getElementById('startButton').disabled = true;
                document.getElementById('captureButton').disabled = true;
                document.getElementById('completeButton').disabled = true;
                return;
            }

            if (sampleCount > 0) {
                updateProgress();
                showMessage(`✓ You have ${sampleCount} samples already. Capture more or complete the enrollment.`, 'info');
            }
        },
        error: function(xhr, status, error) {
            console.log('Note: Could not check enrollment status:', status, error);
        }
    });
}

// ==================== CAMERA OPERATIONS ====================

/**
 * Start webcam and prepare for face capture
 */
async function startCamera() {
    if (!modelsLoaded) {
        showMessage('⏳ Models still loading...', 'warning');
        return;
    }

    console.log('📷 Starting camera...');
    const video = document.getElementById('video');

    if (!video) {
        showMessage('❌ Video element not found', 'error');
        return;
    }

    try {
        videoStream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            },
            audio: false
        });

        video.srcObject = videoStream;
        video.addEventListener('loadedmetadata', () => {
            console.log('✓ Camera started');
            showMessage('✓ Camera is ready. Position your face clearly and click "Capture Face".', 'success');
        });

        // UI changes
        document.getElementById('startButton').style.display = 'none';
        document.getElementById('captureButton').style.display = 'block';
        document.getElementById('resetButton').style.display = 'block';

    } catch (error) {
        handleCameraError(error);
    }
}

/**
 * Handle camera errors
 */
function handleCameraError(error) {
    let message = 'Camera error: ' + error.message;

    if (error.name === 'NotAllowedError') {
        message = 'Camera permission denied. Allow camera access in browser settings.';
    } else if (error.name === 'NotFoundError') {
        message = 'No camera found. Ensure your device has a working camera.';
    } else if (error.name === 'NotReadableError') {
        message = 'Camera is being used by another application.';
    }

    showMessage('❌ ' + message, 'error');
    console.error('Camera error:', error);
    document.getElementById('startButton').disabled = false;
}

// ==================== FACE CAPTURE ====================

/**
 * Capture a single face from the video stream
 */
async function captureFace() {
    if (sampleCount >= ENROLLMENT_CONFIG.MAX_SAMPLES) {
        showMessage(`❌ Maximum ${ENROLLMENT_CONFIG.MAX_SAMPLES} samples reached`, 'error');
        document.getElementById('captureButton').disabled = true;
        return;
    }

    if (isEnrolling) {
        showMessage('⏳ Processing previous capture...', 'info');
        return;
    }

    isEnrolling = true;
    document.getElementById('captureButton').disabled = true;
    showMessage('🔍 Detecting face...', 'info');

    try {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        
        // Verify video is ready
        if (!video || !video.srcObject || video.videoWidth === 0) {
            showMessage('⚠️ Camera not ready. Please check camera permissions.', 'warning');
            document.getElementById('captureButton').disabled = false;
            isEnrolling = false;
            return;
        }
        
        const ctx = canvas.getContext('2d');

        // Detect face with better timeout handling
        console.log(`📹 Video ready: ${video.videoWidth}x${video.videoHeight}`);
        console.log('🔍 Detecting face in video...');
        const detection = await detectFaceWithTimeout(video);

        if (!detection) {
            showMessage('⚠️ No face detected. Ensure good lighting, face centered, 30-60cm away.', 'warning');
            console.warn('No face detected in frame');
            document.getElementById('captureButton').disabled = false;
            isEnrolling = false;
            return;
        }

        // Check face quality
        const quality = validateFaceQuality(detection);
        if (!quality.isValid) {
            showMessage(`⚠️ ${quality.message}`, 'warning');
            document.getElementById('captureButton').disabled = false;
            isEnrolling = false;
            return;
        }

        // Draw face and capture
        console.log('✓ Face detected. Quality check: PASS');
        console.log('  Score:', detection.detection.score.toFixed(4));
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Extract the face descriptor (128-dimensional array)
        const descriptor = detection.descriptor;
        
        if (!descriptor || descriptor.length !== 128) {
            showMessage('⚠️ Failed to extract face descriptor. Please try again.', 'warning');
            console.error('Descriptor extraction failed. Length:', descriptor ? descriptor.length : 'null');
            document.getElementById('captureButton').disabled = false;
            isEnrolling = false;
            return;
        }

        // Convert Float32Array to regular array for JSON serialization
        const descriptorArray = Array.from(descriptor);

        // Send to server (non-blocking AJAX call)
        console.log(`📤 Sending sample ${sampleCount + 1} to server...`);
        sendFaceSampleToServer(descriptorArray);

    } catch (error) {
        console.error('❌ Capture error:', error);
        showMessage(`❌ Error capturing face: ${error.message}`, 'error');
        document.getElementById('captureButton').disabled = false;
        isEnrolling = false;
    }
}

/**
 * Detect a single face with timeout
 */
async function detectFaceWithTimeout(video) {
    return new Promise((resolve, reject) => {
        const timeout = setTimeout(() => {
            reject(new Error('Face detection timeout after ' + ENROLLMENT_CONFIG.DETECTION_TIMEOUT + 'ms'));
        }, ENROLLMENT_CONFIG.DETECTION_TIMEOUT);

        faceapi
            .detectSingleFace(video)
            .withFaceLandmarks()
            .withFaceDescriptor()
            .then(detection => {
                clearTimeout(timeout);
                resolve(detection);
            })
            .catch(error => {
                clearTimeout(timeout);
                console.error('Detection promise error:', error);
                resolve(null); // No face found
            });
    });
}

/**
 * Validate face quality based on detection metrics
 */
function validateFaceQuality(detection) {
    if (!detection) {
        return { isValid: false, message: 'No face detected' };
    }

    const { detection: box, landmarks, descriptor } = detection;

    if (!box || !landmarks) {
        return { isValid: false, message: 'Invalid face detection' };
    }

    // Check face size (should be at least 100x100 pixels)
    const minFaceSize = 100;
    if (box.width < minFaceSize || box.height < minFaceSize) {
        return { isValid: false, message: 'Face too small. Move closer to camera.' };
    }

    // Check face in frame (should be roughly centered)
    const frameArea = box.width * box.height;
    const totalArea = 640 * 480;
    const framePercentage = (frameArea / totalArea) * 100;

    if (framePercentage < 10) {
        return { isValid: false, message: 'Face not prominent enough in frame.' };
    }

    if (framePercentage > 90) {
        return { isValid: false, message: 'Face too close to camera.' };
    }

    // Check landmark confidence
    if (!landmarks || landmarks.length === 0) {
        return { isValid: false, message: 'Cannot detect face landmarks.' };
    }

    return { isValid: true, message: 'Face quality OK' };
}

/**
 * Send captured face descriptor to server
 */
function sendFaceSampleToServer(descriptorArray) {
    $.ajax({
        url: '{{ route("face-enrollment.save-sample") }}',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ face_descriptor: descriptorArray }),
        timeout: 10000,
        success: function(data) {
            if (data.success) {
                sampleCount = data.sample_count;
                updateProgress();

                const remaining = ENROLLMENT_CONFIG.MAX_SAMPLES - sampleCount;
                showMessage(
                    `✓ Sample ${sampleCount} captured successfully. ${remaining} more to go (${ENROLLMENT_CONFIG.MIN_SAMPLES} minimum).`,
                    'success'
                );

                console.log(`✓ Sample ${sampleCount} saved`);

                // Show complete button if minimum reached
                if (sampleCount >= ENROLLMENT_CONFIG.MIN_SAMPLES) {
                    document.getElementById('completeButton').style.display = 'block';
                }

                // Auto-save quality warning
                if (sampleCount === 1) {
                    showMessage('💡 Tip: Capture samples from different angles for better recognition.', 'info');
                }
            } else {
                const errorMsg = data.message || 'Failed to save sample';
                console.error('❌ Server error:', errorMsg);
                showMessage(`❌ ${errorMsg}`, 'error');
            }
            
            // Re-enable capture button
            isEnrolling = false;
            document.getElementById('captureButton').disabled = false;
        },
        error: function(xhr, status, error) {
            let errorMsg = error;
            
            if (status === 'timeout') {
                errorMsg = 'Request timeout. Please try again.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            console.error('❌ Server error:', errorMsg);
            showMessage(`❌ ${errorMsg}`, 'error');
            
            // Re-enable capture button
            isEnrolling = false;
            document.getElementById('captureButton').disabled = false;
        }
    });
}

// ==================== ENROLLMENT COMPLETION ====================

/**
 * Complete the face enrollment process
 */
function completeEnrollment() {
    if (sampleCount < ENROLLMENT_CONFIG.MIN_SAMPLES) {
        showMessage(
            `⚠️ You need at least ${ENROLLMENT_CONFIG.MIN_SAMPLES} samples. You have ${sampleCount}.`,
            'warning'
        );
        return;
    }

    if (!confirm(`Complete enrollment with ${sampleCount} samples? This cannot be undone immediately.`)) {
        return;
    }

    console.log('🔐 Completing enrollment...');
    document.getElementById('completeButton').disabled = true;
    showMessage('⏳ Completing enrollment...', 'info');

    $.ajax({
        url: '{{ route("face-enrollment.complete") }}',
        type: 'POST',
        contentType: 'application/json',
        timeout: 8000,
        success: function(data) {
            if (data.success) {
                console.log('✅ Enrollment completed');
                showMessage('✓ Face enrollment completed successfully! Redirecting...', 'success');

                // Stop camera
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                }

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = data.redirect || '{{ route("employee.dashboard") }}';
                }, 2000);
            } else {
                throw new Error(data.message || 'Enrollment failed');
            }
        },
        error: function(xhr, status, error) {
            let errorMsg = error;
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            console.error('❌ Completion error:', errorMsg);
            showMessage(`❌ ${errorMsg}`, 'error');
            document.getElementById('completeButton').disabled = false;
        }
    });
}

// ==================== RESET ENROLLMENT ====================

/**
 * Reset enrollment and delete all samples
 */
function resetEnrollment() {
    if (!confirm('Are you sure? This will delete all captured samples. You\'ll need to start over.')) {
        return;
    }

    console.log('🔄 Resetting enrollment...');
    showMessage('⏳ Resetting enrollment...', 'info');

    $.ajax({
        url: '{{ route("face-enrollment.reset") }}',
        type: 'POST',
        contentType: 'application/json',
        timeout: 8000,
        success: function(data) {
            sampleCount = 0;
            updateProgress();

            console.log('✓ Enrollment reset');
            showMessage('✓ Enrollment reset. You can start over.', 'success');

            // Reset UI
            document.getElementById('startButton').style.display = 'block';
            document.getElementById('captureButton').style.display = 'none';
            document.getElementById('completeButton').style.display = 'none';
            document.getElementById('resetButton').style.display = 'none';
        },
        error: function(xhr, status, error) {
            let errorMsg = error;
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            console.error('❌ Reset error:', errorMsg);
            showMessage(`❌ Failed to reset: ${errorMsg}`, 'error');
        }
    });
}

// ==================== UI UTILITIES ====================

/**
 * Update progress bar and sample count display
 */
function updateProgress() {
    const progressBar = document.getElementById('progressBar');
    const sampleCountDiv = document.getElementById('sampleCount');

    if (progressBar) {
        const percentage = (sampleCount / ENROLLMENT_CONFIG.MAX_SAMPLES) * 100;
        progressBar.style.width = percentage + '%';
    }

    if (sampleCountDiv) {
        sampleCountDiv.textContent = sampleCount;
    }

    console.log(`Progress: ${sampleCount}/${ENROLLMENT_CONFIG.MAX_SAMPLES}`);
}

/**
 * Display message to user
 */
function showMessage(message, type = 'info') {
    const messageDiv = document.getElementById('messageDiv');
    if (!messageDiv) {
        console.log(`[${type}] ${message}`);
        return;
    }

    const typeMap = {
        'error': 'danger',
        'success': 'success',
        'warning': 'warning',
        'info': 'info'
    };

    const bsType = typeMap[type] || 'info';
    messageDiv.innerHTML = message;
    messageDiv.className = `alert alert-${bsType} alert-dismissible fade show`;
    messageDiv.style.display = 'block';

    // Add close button
    if (!messageDiv.querySelector('.btn-close')) {
        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close';
        closeBtn.setAttribute('data-bs-dismiss', 'alert');
        closeBtn.setAttribute('aria-label', 'Close');
        messageDiv.appendChild(closeBtn);
    }

    // Auto-hide after 6 seconds for non-info
    if (type !== 'info') {
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 6000);
    }
}

/**
 * Get CSRF token from meta tag
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// Export for debugging
window.faceEnrollment = {
    isCameraReady: () => videoStream !== null,
    modelsLoaded: () => modelsLoaded,
    sampleCount: () => sampleCount,
    startCamera,
    captureFace,
    completeEnrollment,
    resetEnrollment
};

console.log('✓ Face Enrollment JavaScript loaded. Use window.faceEnrollment for debugging.');
