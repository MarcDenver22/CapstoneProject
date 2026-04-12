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

        .back-home-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 12px;
            background: rgba(156, 163, 175, 0.2);
            border: 1px solid rgba(156, 163, 175, 0.4);
            color: #d1d5db;
            border-radius: 12px;
            cursor: pointer;
            font-size: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
        }

        .back-home-button:hover {
            background: rgba(156, 163, 175, 0.35);
            border-color: rgba(156, 163, 175, 0.6);
            transform: translateY(-2px);
        }

        .scanner-card {
            position: relative;
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
            <button class="back-home-button" id="backHomeButton" title="Go back to home">←</button>
            <div class="title">📷 Attendance Scanner</div>
            <div class="subtitle">Face Recognition System</div>

            <!-- Registration Number Input -->
            <div id="loginForm" style="margin-bottom: 30px; padding: 20px; background: rgba(59, 130, 246, 0.1); border: 2px solid rgba(59, 130, 246, 0.3); border-radius: 16px;">
                <label style="display: block; color: rgba(255, 255, 255, 0.7); font-size: 14px; margin-bottom: 8px; text-transform: uppercase; font-weight: 600;">Enter Your Employee ID</label>
                <input type="text" id="employeeIdInput" placeholder="e.g., EMP001" style="width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(59, 130, 246, 0.5); border-radius: 8px; color: white; font-size: 16px; margin-bottom: 12px;" autocomplete="off">
                <button id="findUserBtn" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    Find Employee
                </button>
            </div>

            <div class="employee-info" id="employeeInfo">
                <div class="checkmark-icon">✓</div>
                <div class="label">Logged In As</div>
                <div class="name" id="employeeName"></div>
                <div class="details" id="employeeDetails"></div>
            </div>

            <div class="camera-preview-container" id="cameraContainer">
                <div class="preview-wrapper">
                    <video id="videoPreview" autoplay playsinline muted></video>
                    <canvas id="detectionCanvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
                    <div class="preview-overlay"></div>
                </div>
                <div class="instructions" id="instructions">
                    �️ Please face the camera and blink your eyes to record attendance
                </div>
                <div id="detectionInfo" style="color: #60a5fa; font-size: 14px; margin-top: 10px; display: none;">
                    <span id="faceDetectionStatus">Detecting...</span>
                </div>
            </div>

            <button class="scan-button" id="scanButton">
                <span>📱 START DETECTION</span>
            </button>

            <div class="status-message" id="statusMessage"></div>

    </div>

    <!-- Include face-api.js library -->
    <script async src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

    <!-- Include jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- CSRF Token for security -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Load scanner module -->
    <script src="/js/face-scanner.js"></script>
