/**
 * Face Recognition Kiosk Scanner
 * 
 * This module handles real-time face detection and attendance recording
 * with automatic eye blink detection and manual button backup.
 * 
 * Features:
 * - Real-time face detection and matching
 * - Eye aspect ratio (EAR) based blink detection
 * - Automatic attendance recording on complete blink
 * - Manual "Record Attendance" button as fallback
 * - Period-specific time recording (AM/PM)
 * 
 * Dependencies: face-api.js v0.22.2, jQuery
 * Models Location: /storage/models
 */

// ==================== JQUERY AJAX SETUP ====================
$.ajaxSetup({
    timeout: 8000,
    dataType: 'json',
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    beforeSend: function(xhr) {
        // Add CSRF token to each request
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        }
    },
    error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
    }
});

// Get CSRF token for use in manual AJAX calls
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

// ==================== GLOBAL STATE ====================
let modelsLoaded = false;
let isScanning = false;
let cooldownActive = false;
let detectionInterval = null;
let stream = null;

// User session
let userFound = false;
let currentUserId = null;
let currentUserName = null;
let enrolledDescriptor = null;

// Eye blink tracking
let lastEyeState = null;
let blinkConfirmed = false;
let detectedFaceCount = 0;
let isFaceDetected = false;
let closedEyeFrames = 0;  // Counter for consecutive closed eye frames
let openEyeFrames = 0;    // Counter for consecutive open eye frames

// DOM elements
const videoPreview = document.getElementById('videoPreview');
const detectionCanvas = document.getElementById('detectionCanvas');
const cameraContainer = document.getElementById('cameraContainer');
const statusMessage = document.getElementById('statusMessage');
const employeeInfo = document.getElementById('employeeInfo');
const faceDetectionStatus = document.getElementById('faceDetectionStatus');
const detectionInfo = document.getElementById('detectionInfo');
const loginForm = document.getElementById('loginForm');
const employeeIdInput = document.getElementById('employeeIdInput');
const findUserBtn = document.getElementById('findUserBtn');

// Configuration
const CONFIG = {
    DETECTION_INTERVAL: 100,
    FACE_DETECTION_THRESHOLD: 3,
    COOLDOWN_DURATION: 5000,
    CONFIDENCE_THRESHOLD: 0.6,
    FACE_MATCH_THRESHOLD: 0.6,      // Euclidean distance threshold for face matching
    BLINK_THRESHOLD: 0.25,           // Eye openness threshold (open eyes ~0.28+, closed eyes ~0.20-)
    MODEL_PATH: '/storage/models/'
};

// ==================== INITIALIZATION ====================

/**
 * Initialize scanner on page load
 */
window.addEventListener('load', async () => {
    console.log('🚀 Kiosk scanner initializing...');
    showStatus('Loading AI models...', 'scanning');
    
    try {
        if (typeof faceapi === 'undefined') {
            throw new Error('face-api.js not loaded');
        }

        // Load models
        await Promise.all([
            faceapi.nets.ssdMobilenetv1.loadFromUri(CONFIG.MODEL_PATH),
            faceapi.nets.faceRecognitionNet.loadFromUri(CONFIG.MODEL_PATH),
            faceapi.nets.faceLandmark68Net.loadFromUri(CONFIG.MODEL_PATH),
        ]);

        modelsLoaded = true;
        console.log('✅ Models loaded');
        showStatus('Enter your Employee ID to begin', 'idle');

        // Focus on employee ID input
        employeeIdInput.focus();

    } catch (error) {
        console.error('❌ Initialization failed:', error);
        showStatus('Failed to load models: ' + error.message, 'error');
    }

    // Attach event listeners
    attachEventListeners();
});

/**
 * Attach event listeners to buttons
 */
function attachEventListeners() {
    findUserBtn.addEventListener('click', handleFindUser);
    employeeIdInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') findUserBtn.click();
    });
    document.getElementById('backHomeButton')?.addEventListener('click', () => {
        resetScanner();
        // Navigate back to home after reset
        setTimeout(() => {
            window.location.href = '/';
        }, 300);
    });
}

/**
 * Clean up camera when leaving page
 */
window.addEventListener('beforeunload', () => {
    stopCamera();
});

// ==================== EMPLOYEE LOOKUP ====================

/**
 * Handle employee lookup by ID
 */
function handleFindUser() {
    const employeeId = employeeIdInput.value.trim();
    
    if (!employeeId) {
        alert('Please enter your Employee ID');
        return;
    }

    // Stop any ongoing camera/detection
    stopCamera();
    enrolledDescriptor = null;

    findUserBtn.disabled = true;
    findUserBtn.textContent = 'Searching...';

    $.ajax({
        url: '/kiosk/find-user',
        type: 'POST',
        data: JSON.stringify({ employee_id: employeeId }),
        contentType: 'application/json',
        timeout: 5000,
        success: function(data) {
            if (data.status === 'success') {
                userFound = true;
                currentUserId = data.user_id;
                currentUserName = data.name;
                showStatus(data.message, 'success');
                
                // Clear old success screen data
                document.getElementById('employeeDetails').innerHTML = '';
                
                // Hide login form and show employee info
                loginForm.style.display = 'none';
                employeeInfo.classList.add('show');
                document.getElementById('employeeName').textContent = data.name;
                
                // Start camera for scanning
                startCameraForScanning();
            } else {
                throw new Error(data.message);
            }
        },
        error: function(xhr, status, error) {
            let message = 'Error: ' + error;
            if (xhr.status === 404) {
                message = 'ID not found. Check your ID.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            showStatus(message, 'error');
        },
        complete: function() {
            findUserBtn.disabled = false;
            findUserBtn.textContent = 'Log In';
        }
    });
}

// ==================== CAMERA SETUP ====================

/**
 * Start camera for scanning with enrolled face data
 */
async function startCameraForScanning() {
    cameraContainer.classList.add('active');
    cameraContainer.style.display = 'block';
    showStatus('📷 Loading enrolled face data...', 'scanning');
    
    // Load the enrolled descriptor first
    const descriptorLoaded = await loadEnrolledDescriptor();
    if (!descriptorLoaded) {
        showStatus('❌ Failed to load face enrollment. Please try again.', 'error');
        cameraContainer.classList.remove('active');
        return;
    }
    
    await startCamera();
}

/**
 * Start camera stream
 */
async function startCamera() {
    try {
        console.log('📷 Starting camera...');
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            },
            audio: false
        });

        videoPreview.srcObject = stream;
        console.log('✓ Camera stream started');

        cameraContainer.classList.add('active');
        detectionInfo.style.display = 'block';
        
        // Start face detection loop
        startFaceDetection();
        showStatus('🎥 Detecting faces...', 'scanning');

    } catch (error) {
        console.error('❌ Camera error:', error);
        let msg = 'Camera access failed: ';
        if (error.name === 'NotAllowedError') msg += 'Permission denied.';
        else if (error.name === 'NotFoundError') msg += 'No camera found.';
        else msg += error.message;
        showStatus(msg, 'error');
    }
}

/**
 * Stop camera and cleanup
 */
function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }

    if (detectionInterval) {
        clearInterval(detectionInterval);
        detectionInterval = null;
    }

    cameraContainer.classList.remove('active');
    cameraContainer.style.display = 'none';
    detectionInfo.style.display = 'none';
    detectedFaceCount = 0;
    isFaceDetected = false;
    enrolledDescriptor = null;
    lastEyeState = null;
    blinkConfirmed = false;
}

// ==================== DETECTION TOGGLE ====================


// ==================== FACE DETECTION ====================

/**
 * Start continuous face detection loop
 */
function startFaceDetection() {
    const ctx = detectionCanvas.getContext('2d');
    const displaySize = {
        width: videoPreview.offsetWidth,
        height: videoPreview.offsetHeight
    };

    detectionCanvas.width = displaySize.width;
    detectionCanvas.height = displaySize.height;

    detectionInterval = setInterval(async () => {
        try {
            // Detect all faces
            const detections = await faceapi
                .detectAllFaces(videoPreview)
                .withFaceLandmarks()
                .withFaceDescriptors();

            const resized = faceapi.resizeResults(detections, displaySize);

            // Clear canvas
            ctx.clearRect(0, 0, detectionCanvas.width, detectionCanvas.height);

            if (resized.length === 0) {
                detectedFaceCount = 0;
                isFaceDetected = false;
                faceDetectionStatus.textContent = '⏳ No face detected';
                lastEyeState = null;
                blinkConfirmed = false;
                return;
            }

            // ===== FACE MATCHING =====
            const detection = resized[0];
            const detectedDescriptor = Array.from(detection.descriptor);
            
            // Calculate distance from enrolled descriptor
            const matchDistance = enrolledDescriptor ? euclideanDistance(detectedDescriptor, enrolledDescriptor) : Infinity;
            const isMatched = matchDistance < CONFIG.FACE_MATCH_THRESHOLD;
            
            console.log('Face matching:', {
                detected: true,
                distance: matchDistance.toFixed(4),
                threshold: CONFIG.FACE_MATCH_THRESHOLD,
                matched: isMatched
            });

            const box = detection.detection.box;

            // ===== EYE BLINK DETECTION =====
            const landmarksPositions = detection.landmarks.positions || detection.landmarks;
            const eyeState = detectEyeState(landmarksPositions);
            let blinkDetectedText = '';

            // Only update counters if we have a valid eye state
            if (eyeState !== null) {
                if (eyeState === 'open') {
                    // Reset closed counter when eyes open
                    if (closedEyeFrames > 0) {
                        console.log(`👁️ Eyes opening after ${closedEyeFrames} closed frames`);
                        
                        // A real blink = eyes were closed for 2+ frames and now opening
                        if (isMatched && closedEyeFrames >= 2 && !blinkConfirmed && lastEyeState === 'closed') {
                            console.log('✅ VALID BLINK DETECTED!');
                            blinkConfirmed = true;
                            blinkDetectedText = ' (BLINK!)';
                            
                            // Trigger scan
                            if (!isScanning && !cooldownActive) {
                                console.log('🚀 Triggering performScan from blink');
                                performScan().catch(err => console.error('Scan error:', err));
                            }
                        }
                    }
                    
                    closedEyeFrames = 0;
                    openEyeFrames++;
                } else if (eyeState === 'closed') {
                    // Reset open counter when eyes close
                    openEyeFrames = 0;
                    closedEyeFrames++;
                    blinkDetectedText = ' (Closing...)';
                }
                
                // Log less frequently to reduce spam
                if ((openEyeFrames + closedEyeFrames) % 5 === 0) {
                    console.log(`👁️ State: ${eyeState} | Open: ${openEyeFrames} | Closed: ${closedEyeFrames} | Last: ${lastEyeState} | Blink: ${blinkConfirmed}`);
                }
                
                // Update lastEyeState
                lastEyeState = eyeState;
            } else {
                // If eye state is null, reset counters
                closedEyeFrames = 0;
                openEyeFrames = 0;
            }
            
            // Reset blink confirmation after detection
            if (blinkConfirmed && eyeState === 'open' && openEyeFrames >= 5) {
                console.log('👁️ Resetting blink state');
                blinkConfirmed = false;
                closedEyeFrames = 0;
                openEyeFrames = 0;
            }

            // Draw detection box
            const color = isMatched ? '#10b981' : '#ef4444';
            ctx.strokeStyle = color;
            ctx.lineWidth = 3;
            ctx.strokeRect(box.x, box.y, box.width, box.height);

            // Draw label
            const label = isMatched ? '✓ Face Matched' : '✗ Face Not Matched';
            ctx.fillStyle = color;
            ctx.fillRect(box.x, box.y - 30, 270, 30);
            ctx.fillStyle = 'white';
            ctx.font = 'bold 14px Arial';
            ctx.fillText(label, box.x + 5, box.y - 12);

            // Update status
            const eyeStatusText = eyeState === null ? '👁️ Unable to detect eyes' : (eyeState === 'open' ? '👁️ Eyes Open' : '👁️ Eyes Closed');
            faceDetectionStatus.textContent = label + ` (${matchDistance.toFixed(3)}) ${eyeStatusText}${blinkDetectedText}`;
            
            if (isMatched) {
                isFaceDetected = true;
                detectedFaceCount++;
            } else {
                isFaceDetected = false;
            }

        } catch (error) {
            console.error('Detection error:', error);
        }
    }, CONFIG.DETECTION_INTERVAL);
}

// ==================== FACE MATCHING UTILITIES ====================

/**
 * Detect eye state (open/closed) using eye aspect ratio
 * @param {Array} landmarks - Facial landmarks from face-api
 * @returns {string|null} - 'open', 'closed', or null
 */
function detectEyeState(landmarks) {
    // Handle both array and FaceLandmarks object formats
    let landmarksArray = landmarks;
    
    if (landmarks && typeof landmarks === 'object' && !Array.isArray(landmarks) && landmarks.positions) {
        landmarksArray = landmarks.positions;
    }

    if (!landmarksArray || !Array.isArray(landmarksArray) || landmarksArray.length < 48) {
        console.warn('Invalid landmarks array:', landmarksArray?.length || 'undefined');
        return null;
    }

    // Eye landmarks (left eye: 36-41, right eye: 42-47)
    const leftEye = [
        landmarksArray[36], landmarksArray[37], landmarksArray[38],
        landmarksArray[39], landmarksArray[40], landmarksArray[41]
    ];
    
    const rightEye = [
        landmarksArray[42], landmarksArray[43], landmarksArray[44],
        landmarksArray[45], landmarksArray[46], landmarksArray[47]
    ];

    // Validate eye landmarks
    const allEyePoints = [...leftEye, ...rightEye];
    if (allEyePoints.some(point => !point || typeof point.x !== 'number' || typeof point.y !== 'number')) {
        console.warn('Invalid eye landmark points');
        return null;
    }

    try {
        // Calculate eye aspect ratio (EAR)
        const leftEyeAR = (
            distance(leftEye[1], leftEye[5]) +
            distance(leftEye[2], leftEye[4])
        ) / (2 * distance(leftEye[0], leftEye[3]));

        const rightEyeAR = (
            distance(rightEye[1], rightEye[5]) +
            distance(rightEye[2], rightEye[4])
        ) / (2 * distance(rightEye[0], rightEye[3]));

        const avgEAR = (leftEyeAR + rightEyeAR) / 2;
        const eyeState = avgEAR < CONFIG.BLINK_THRESHOLD ? 'closed' : 'open';
        
        // Log EAR values EVERY frame for debugging
        console.log(`EAR: ${avgEAR.toFixed(4)} | L: ${leftEyeAR.toFixed(4)} | R: ${rightEyeAR.toFixed(4)} | State: ${eyeState} | Threshold: ${CONFIG.BLINK_THRESHOLD}`);
        
        return eyeState;
    } catch (error) {
        console.error('Error calculating eye aspect ratio:', error);
        return null;
    }
}

/**
 * Calculate Euclidean distance between two 2D points
 * @param {Object} p1 - Point with x, y properties
 * @param {Object} p2 - Point with x, y properties
 * @returns {number} - Distance
 */
function distance(p1, p2) {
    if (!p1 || !p2 || typeof p1.x !== 'number' || typeof p1.y !== 'number' || typeof p2.x !== 'number' || typeof p2.y !== 'number') {
        console.warn('Invalid points for distance calculation:', p1, p2);
        return 0;
    }
    return Math.sqrt(Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2));
}

/**
 * Calculate Euclidean distance between two 128-dimensional descriptors
 * @param {Array} descriptor1 - 128-d face descriptor
 * @param {Array} descriptor2 - 128-d face descriptor
 * @returns {number} - Distance
 */
function euclideanDistance(descriptor1, descriptor2) {
    if (!descriptor1 || !descriptor2 || descriptor1.length !== 128 || descriptor2.length !== 128) {
        return Infinity;
    }
    
    let sum = 0;
    for (let i = 0; i < 128; i++) {
        const diff = (descriptor1[i] || 0) - (descriptor2[i] || 0);
        sum += diff * diff;
    }
    
    return Math.sqrt(sum);
}

/**
 * Load enrolled face descriptor for current user
 */
async function loadEnrolledDescriptor() {
    try {
        const response = await $.ajax({
            url: '/kiosk/get-user-descriptor',
            type: 'GET',
            dataType: 'json',
            timeout: 5000
        });

        if (response.status === 'success' && response.descriptor) {
            enrolledDescriptor = response.descriptor;
            console.log('✅ Enrolled descriptor loaded for:', response.employee_id);
            return true;
        } else {
            console.error('Failed to load descriptor:', response.message);
            return false;
        }
    } catch (error) {
        console.error('Error loading enrolled descriptor:', error);
        return false;
    }
}

// ==================== ATTENDANCE RECORDING ====================

/**
 * Handle automatic recording triggered by blink or manual button
 */
async function performScan() {
    try {
        console.log('🔄 performScan() called - isScanning:', isScanning, 'cooldownActive:', cooldownActive);
        
        if (isScanning) {
            console.warn('⚠️ Already scanning, ignoring duplicate request');
            return;
        }
        
        isScanning = true;
        showStatus('📸 Extracting face descriptor...', 'scanning');

        // Detect face and extract descriptor
        const detection = await faceapi
            .detectSingleFace(videoPreview)
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!detection || !detection.descriptor) {
            showStatus('⚠️ No face detected. Please look at the camera.', 'error');
            isScanning = false;
            return;
        }

        console.log('✓ Face detected');
        const descriptorArray = Array.from(detection.descriptor);
        console.log('  Descriptor length:', descriptorArray.length);
        console.log('  User ID:', currentUserId);

        showStatus('💾 Sending to server...', 'scanning');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Send descriptor to server for recording
        const scanData = { 
            face_descriptor: descriptorArray,
            user_id: currentUserId 
        };
        
        console.log('📤 Sending AJAX request:', {
            url: '/kiosk/scan',
            timestamp: new Date().toISOString()
        });
        
        $.ajax({
            url: '/kiosk/scan',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: JSON.stringify(scanData),
            timeout: 10000,
            success: function(data) {
                if (data.status === 'success') {
                    console.log('✅ Attendance recorded:', data);
                    
                    // Stop camera
                    stopCamera();
                    
                    // Display success with punch details
                    displayAttendanceSuccess(data);
                    showStatus('✓ ' + data.message, 'success');

                    // Auto-reset after cooldown
                    setTimeout(() => {
                        resetScanner();
                    }, CONFIG.COOLDOWN_DURATION);

                    setCooldown();
                } else {
                    showStatus('✗ ' + data.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX Error:', status, error);
                console.error('  Response:', xhr.responseJSON);
                
                let errorMsg = 'Server error. Please try again.';
                if (status === 'timeout') {
                    errorMsg = 'Request timeout. Please try again.';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                
                showStatus(errorMsg, 'error');
            },
            complete: function() {
                isScanning = false;
            }
        });

    } catch (error) {
        console.error('❌ Scan failed:', error);
        showStatus('❌ ' + error.message, 'error');
        isScanning = false;
    }
}



/**
 * Display success message with punch details
 */
function displayAttendanceSuccess(data) {
    const employeeName = data.name || 'Employee';
    const isArrival = data.punch_type === 'IN';
    const punchTime = isArrival ? data.time_in : data.time_out;
    const statusIcon = isArrival ? '🔓' : '🔒';
    const statusLabel = isArrival ? 'ARRIVAL' : 'DEPARTURE';
    const periodIcon = data.period === 'AM' ? '🌅' : '🌆';
    
    document.getElementById('employeeName').innerHTML = `
        <h2 style="font-size: 32px; margin: 10px 0; color: #10b981;">${employeeName}</h2>
    `;
    document.getElementById('employeeDetails').innerHTML = `
        <div style="font-size: 48px; margin: 20px 0;">${statusIcon}</div>
        <div style="font-size: 28px; font-weight: bold; color: ${isArrival ? '#10b981' : '#f59e0b'}; margin-bottom: 15px;">
            ${statusLabel}
        </div>
        <div style="font-size: 20px; color: #ccc; margin-bottom: 10px;">
            ${periodIcon} ${data.period === 'AM' ? 'Morning' : 'Afternoon'}
        </div>
        <div style="font-size: 24px; font-weight: bold; color: #60a5fa; margin: 20px 0;">
            ${punchTime}
        </div>
        <div style="font-size: 14px; color: #999; margin-top: 10px;">
            Verified by Face Recognition
        </div>
    `;

    employeeInfo.classList.add('show');
}

/**
 * Reset scanner to initial state for next user
 */
function resetScanner() {
    // Stop camera first
    stopCamera();
    
    // Clear all employee data from success screen
    document.getElementById('employeeName').textContent = '';
    document.getElementById('employeeDetails').innerHTML = '';
    
    employeeInfo.classList.remove('show');
    loginForm.style.display = 'block';
    cameraContainer.style.display = 'none';
    document.getElementById('employeeIdInput').value = '';
    userFound = false;
    currentUserId = null;
    currentUserName = null;
}

/**
 * Set cooldown period after successful scan
 */
function setCooldown() {
    cooldownActive = true;
    
    setTimeout(() => {
        cooldownActive = false;
    }, CONFIG.COOLDOWN_DURATION);
}

// ==================== UI UTILITIES ====================

/**
 * Show status message
 */
function showStatus(message, type = 'info') {
    console.log(`[${type.toUpperCase()}] ${message}`);
    
    if (!statusMessage) return;

    statusMessage.textContent = message;
    statusMessage.className = 'status-message';
    statusMessage.classList.add(`status-${type}`);

    // Auto-hide after 3 seconds for non-error messages
    if (type !== 'error') {
        setTimeout(() => {
            statusMessage.textContent = '';
            statusMessage.className = 'status-message';
        }, 3000);
    }
}
