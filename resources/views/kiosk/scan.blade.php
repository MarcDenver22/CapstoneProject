<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kiosk Attendance Scanner</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
        }

        .scanner-card {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-radius: 32px;
            padding: 60px 40px;
            text-align: center;
            color: white;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 25px 80px rgba(59, 130, 246, 0.2);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .title {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 12px;
            color: #60a5fa;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 50px;
            letter-spacing: 0.5px;
        }

        .camera-preview-container {
            display: none;
            margin-bottom: 30px;
        }

        .camera-preview-container.active {
            display: block;
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .preview-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 0 auto 20px;
            aspect-ratio: 4/3;
            overflow: hidden;
            border-radius: 20px;
            border: 3px solid rgba(59, 130, 246, 0.6);
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.3);
            background: #000;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-overlay {
            position: absolute;
            top: 10%;
            left: 10%;
            width: 80%;
            height: 80%;
            border: 3px solid rgba(16, 185, 129, 0.6);
            border-radius: 24px;
            pointer-events: none;
            animation: borderPulse 2s ease-in-out infinite;
        }

        @keyframes borderPulse {
            0%, 100% { box-shadow: inset 0 0 20px rgba(16, 185, 129, 0.2); }
            50% { box-shadow: inset 0 0 30px rgba(16, 185, 129, 0.4); }
        }

        .instructions {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.3);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
        }

        .scan-button {
            width: 100%;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.4s ease;
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .scan-button:hover:not(:disabled) {
            transform: translateY(-4px);
            box-shadow: 0 30px 90px rgba(59, 130, 246, 0.6);
        }

        .scan-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: linear-gradient(135deg, #64748b 0%, #1e293b 100%);
        }

        .status-message {
            display: none;
            margin-top: 30px;
            padding: 20px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            animation: slideDown 0.4s ease-out;
        }

        .status-message.show {
            display: block;
        }

        .status-message.success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #6ee7b7;
        }

        .status-message.error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #f87171;
        }

        .status-message.scanning {
            background: rgba(59, 130, 246, 0.15);
            border: 1px solid rgba(59, 130, 246, 0.5);
            color: #60a5fa;
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .exit-button {
            margin-top: 20px;
            padding: 12px 24px;
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #f87171;
            border-radius: 12px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .exit-button:hover {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(239, 68, 68, 0.6);
        }

        .employee-info {
            display: none;
            margin-bottom: 40px;
            padding: 20px;
            background: rgba(16, 185, 129, 0.15);
            border: 2px solid rgba(16, 185, 129, 0.4);
            border-radius: 16px;
            animation: slideDown 0.5s ease-out;
        }

        .employee-info.show {
            display: block;
        }

        .employee-info .label {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .employee-info .name {
            font-size: 32px;
            font-weight: bold;
            color: #6ee7b7;
            margin-bottom: 12px;
        }

        .employee-info .details {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
        }

        .checkmark-icon {
            font-size: 28px;
            margin-bottom: 12px;
            animation: popIn 0.5s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="scanner-card">
            <div class="title">📷 Attendance Scanner</div>
            <div class="subtitle">Scan your face to record attendance</div>

            <div class="employee-info" id="employeeInfo">
                <div class="checkmark-icon">✓</div>
                <div class="label">Logged In As</div>
                <div class="name" id="employeeName"></div>
                <div class="details" id="employeeDetails"></div>
            </div>

            <div class="camera-preview-container" id="cameraContainer">
                <div class="preview-wrapper">
                    <video id="videoPreview" autoplay playsinline muted></video>
                    <div class="preview-overlay"></div>
                </div>
                <div class="instructions">📷 Please face the camera and hold still</div>
            </div>

            <button class="scan-button" id="scanButton">
                <span>📱 SCAN FACE</span>
            </button>

            <div class="status-message" id="statusMessage"></div>

            <form action="{{ route('kiosk.logout') }}" method="POST" style="width: 100%;">
                @csrf
                <button type="submit" class="exit-button">Exit Kiosk</button>
            </form>
        </div>
    </div>

    <script>
        const scanButton = document.getElementById('scanButton');
        const videoPreview = document.getElementById('videoPreview');
        const cameraContainer = document.getElementById('cameraContainer');
        const statusMessage = document.getElementById('statusMessage');
        const employeeInfo = document.getElementById('employeeInfo');
        const employeeName = document.getElementById('employeeName');
        const employeeDetails = document.getElementById('employeeDetails');

        let stream = null;
        let isScanning = false;
        let cooldownActive = false;
        const SCAN_DELAY = 1000;
        const COOLDOWN_DURATION = 5000;

        async function startCamera() {
            try {
                console.log('Requesting camera access...');
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 1280 }, height: { ideal: 720 }, facingMode: 'user' },
                    audio: false
                });

                videoPreview.srcObject = stream;
                console.log('Camera stream started');

                videoPreview.onloadedmetadata = function() {
                    console.log('Video playing');
                    videoPreview.play();
                };

                cameraContainer.classList.add('active');
                showStatus('Face the camera and hold still', 'scanning');

            } catch (error) {
                console.error('Camera error:', error.name, error.message);
                let msg = 'Camera access failed: ';
                if (error.name === 'NotAllowedError') msg += 'Permission denied. Allow camera in browser settings.';
                else if (error.name === 'NotFoundError') msg += 'No camera device found.';
                else msg += error.message;
                showStatus(msg, 'error');
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
                cameraContainer.classList.remove('active');
            }
        }

        function captureFrame() {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                canvas.width = videoPreview.videoWidth;
                canvas.height = videoPreview.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(videoPreview, 0, 0);
                resolve(canvas.toDataURL('image/jpeg', 0.9));
            });
        }

        function showStatus(message, type = 'info') {
            statusMessage.textContent = message;
            statusMessage.className = 'status-message show ' + type;
        }

        function setCooldown() {
            cooldownActive = true;
            scanButton.disabled = true;
            let remaining = COOLDOWN_DURATION / 1000;

            const timer = setInterval(() => {
                remaining--;
                scanButton.textContent = '📱 SCAN (' + remaining + 's)';
                if (remaining <= 0) {
                    clearInterval(timer);
                    scanButton.textContent = '📱 SCAN FACE';
                    scanButton.disabled = false;
                    cooldownActive = false;
                }
            }, 1000);
        }

        scanButton.addEventListener('click', async () => {
            console.log('Scan button clicked');
            if (isScanning || cooldownActive) return;

            isScanning = true;
            scanButton.disabled = true;

            try {
                await startCamera();
                await new Promise(resolve => setTimeout(resolve, SCAN_DELAY));
                showStatus('Capturing...', 'scanning');
                const imageBase64 = await captureFrame();
                stopCamera();

                showStatus('Processing...', 'scanning');
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                    || document.querySelector('input[name="_token"]')?.value;
                
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    showStatus('Security error: CSRF token missing', 'error');
                    scanButton.disabled = false;
                    return;
                }

                const response = await fetch('/kiosk/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ image: imageBase64 })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    // Display employee info
                    const fullMessage = data.message;
                    const nameMatch = fullMessage.match(/Attendance recorded for (.+)$/);
                    const displayName = nameMatch ? nameMatch[1] : 'Employee';
                    
                    employeeName.textContent = displayName;
                    employeeDetails.textContent = 
                        (data.period === 'AM' ? '🌅 Morning' : '🌆 Afternoon') + ' - ' + 
                        (data.punch_type === 'IN' ? 'Punch IN' : 'Punch OUT') + ' ✓';
                    
                    employeeInfo.classList.add('show');
                    showStatus('✓ ' + data.message, 'success');
                    
                    // Hide employee info after a few seconds when starting new scan
                    setTimeout(() => {
                        employeeInfo.classList.remove('show');
                    }, COOLDOWN_DURATION - 1000);
                } else {
                    showStatus('✗ ' + data.message, 'error');
                }
                setCooldown();

            } catch (error) {
                console.error('Error:', error);
                showStatus('Error occurred: ' + error.message, 'error');
                stopCamera();
                scanButton.disabled = false;
            } finally {
                isScanning = false;
            }
        });
    </script>
</body>
</html>
