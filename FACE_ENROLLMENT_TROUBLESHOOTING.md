# Face Enrollment Troubleshooting Guide

## ✓ WHAT'S WORKING

1. **Face-API Models**: All 6 model files are loaded (12.5 MB total)
2. **Database**: Descriptors stored correctly as 128-d arrays
3. **API Routes**: All endpoints configured
4. **JavaScript**: All files have proper detection code
5. **PHP**: No syntax errors

## ❌ WHAT MIGHT BE WRONG

### Issue 1: Face Not Detected During Enrollment

**"Unknown" during capture means**: The camera shows video but no face is detected by face-api.js

**Checklist**:
- ✓ Browser: Chrome, Firefox, Safari, or Edge (NOT Internet Explorer)
- ✓ Lighting: Well-lit room (no shadows on face)
- ✓ Camera: Front-facing camera or external webcam
- ✓ Distance: 30-60cm from camera
- ✓ Position: Face centered in frame, directly facing camera
- ✓ Permissions: Allow camera access in browser
- ✓ Network: Good internet (CDN models may be slow on first load)

### Issue 2: Descriptor Not Being Sent to Server

**Quick Test**:
1. Go to: **http://localhost/attendance/public/test-face-api.html**
2. Click "Start Camera"
3. Click "Test Face Detection"
4. Watch the log for messages

**Expected output**:
```
✓ Face detected!
  Score: 0.9234
  Descriptor length: 128
  First 5 values: [0.0123, -0.456, 0.789, ...]
```

If you see "NO FACE DETECTED", the problem is:
1. Your lighting is too dark
2. Face is too far from camera
3. face-api.js models didn't load (check browser console)

### Issue 3: Server Not Receiving Descriptor

**Check browser console** (F12 → Console tab):
- Green messages = OK
- Red errors = Problem

**Common errors**:
- `CORS error` = API endpoint blocked
- `500 error` = Server-side bug
- `Timeout` = Network too slow

### Issue 4: You're Not Enrolled Yet

**Current Status**:
- EMP001 (Marc Denver Riturban): **NOT ENROLLED** ← This is you!
- EMP003 (Employee): **ENROLLED** with 10 samples

**To enroll**:
1. Login to employee dashboard
2. Go to "Face Enrollment" page
3. Click "Start Camera"
4. Capture 3-10 samples
5. Click "Complete Enrollment"

## STEP-BY-STEP SOLUTION

### Step 1: Test Face-API.js (5 minutes)
```
1. Go to: http://localhost/attendance/public/test-face-api.html
2. Click "Start Camera"
3. Allow camera permission
4. Click "Test Face Detection"
5. Look at the log
```

**If face detected**: Problem is enrollment page code → SKIP to Step 3
**If NOT detected**: Problem is camera/lighting → SKIP to Step 2

### Step 2: Fix Camera/Lighting (5 minutes)
- Move to bright room (natural window light is best)
- Sit 30-50cm from camera
- Make sure face is centered and centered in frame
- Retry test on test-face-api.html

### Step 3: Try Fresh Enrollment
```
1. Refresh enrollment page (Ctrl+F5 for hard refresh)
2. Wait for "Models loaded" message
3. Open browser console (F12 → Console)
4. Click "Capture Sample"
5. Look for error messages in console
```

**Expected console messages**:
```
📹 Video dimensions: 640x480
🔍 Starting face detection...
✓ Face detected, descriptor extracted
  Detection confidence: 0.95
  Descriptor length: 128
📤 Sending descriptor to server...
✓ Server response: {success: true, sample_count: 1}
✓ Sample 1 captured!
```

### Step 4: If Still Stuck
Provide these screenshots:
1. Console log from Step 3 (select all, copy)
2. Same log after trying 3 times
3. What you see on test-face-api.html (detected or not)

## TECHNICAL DETAILS

### Database Status
```
EMP001 | Marc Denver Riturban   | NOT enrolled | 0 samples
EMP002 | Admin User              | NOT enrolled | 0 samples  
EMP003 | Employee                | ENROLLED ✓   | 10 samples (working)
EMP004 | HR                       | NOT enrolled | 0 samples
```

### API Validation  
Descriptors must be:
- ✓ Exactly 128 values
- ✓ All numeric floats
- ✓ Typically in range [-1 to +1]
- ✓ Extracted by face-api.js, NOT custom code

### Models Loaded
- ssd_mobilenetv1: Face detection (5.6 MB)
- faceLandmark68: Face landmarks (0.4 MB)  
- faceRecognitionNet: Face descriptor extraction (6.4 MB)
- Total: 12.5 MB

## NEXT STEPS

1. **Quick Check**: Go to test page above
2. **If works**: Your browser + camera are fine → Try fresh enrollment
3. **If fails**: Check lighting and camera position
4. **Still stuck**: Share console logs from F12

Good luck! 🎯
