<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== FACE RECOGNITION ENROLLMENT DIAGNOSTIC ===\n\n";

// 1. Check if face-api models exist
echo "1. Checking face-api.js models...\n";
$models = [
    'ssd_mobilenetv1_model.bin',
    'ssd_mobilenetv1_model-weights_manifest.json',
    'face_landmark_68_model.bin',
    'face_landmark_68_model-weights_manifest.json',
    'face_recognition_model.bin',
    'face_recognition_model-weights_manifest.json',
];

$modelPath = base_path('public/storage/models');
foreach ($models as $model) {
    $file = $modelPath . '/' . $model;
    $exists = file_exists($file);
    $size = $exists ? filesize($file) : 0;
    $status = $exists ? '✓' : '✗';
    echo "  $status $model (" . ($size > 0 ? number_format($size) . ' bytes' : 'NOT FOUND') . ")\n";
}

// 2. Check enrolled users and their descriptor data
echo "\n2. Checking enrolled users...\n";
$users = \App\Models\User::all();
foreach ($users as $u) {
    echo "  {$u->employee_id} | {$u->name}\n";
    echo "    Enrolled: " . ($u->face_enrolled ? 'YES' : 'NO') . "\n";
    echo "    Samples: {$u->face_samples_count}\n";
    
    if ($u->face_enrolled && $u->face_encodings) {
        $descriptors = json_decode($u->face_encodings, true);
        echo "    Encoding format: " . (is_array($descriptors) ? 'ARRAY' : 'STRING/IMAGE') . "\n";
        
        if (is_array($descriptors) && count($descriptors) > 0) {
            if (is_array($descriptors[0])) {
                $first = $descriptors[0];
                echo "    First descriptor length: " . count($first) . " (expected 128)\n";
                echo "    First descriptor sample values: [" . $first[0] . ", " . $first[1] . ", " . $first[2] . ", ...]\n";
            } else {
                echo "    ERROR: Descriptors stored as STRING instead of ARRAY\n";
                echo "    First 50 chars: " . substr($descriptors[0], 0, 50) . "...\n";
            }
        }
    }
    echo "\n";
}

// 3. Check API routes
echo "3. Checking API routes...\n";
$routes = [
    'employee.face.save_sample' => '/face-enrollment/save-sample',
    'employee.face.complete' => '/face-enrollment/complete',
    'employee.face.reset' => '/face-enrollment/reset',
];

foreach ($routes as $name => $path) {
    try {
        $route = \Illuminate\Support\Facades\Route::getRoutes()->getByName($name);
        echo "  ✓ $name => $path\n";
    } catch (\Exception $e) {
        echo "  ✗ $name NOT FOUND\n";
    }
}

// 4. Test descriptor validation
echo "\n4. Testing descriptor validation...\n";

// Create a mock 128-dimensional descriptor
$mockDescriptor = [];
for ($i = 0; $i < 128; $i++) {
    $mockDescriptor[] = mt_rand() / mt_getrandmax() - 0.5;
}

echo "  Created mock descriptor: " . count($mockDescriptor) . " values\n";
echo "  First 5 values: [" . implode(', ', array_slice($mockDescriptor, 0, 5)) . ", ...]\n";
echo "  All numeric? " . (count(array_filter($mockDescriptor, 'is_numeric')) === 128 ? 'YES' : 'NO') . "\n";

// 5. Check JavaScript files
echo "\n5. Checking JavaScript files...\n";
$jsFiles = [
    'public/js/face-enrollment.js',
    'resources/views/employee/face_enrollment.blade.php',
    'resources/views/employee/employee_face_enrollment.blade.php',
];

foreach ($jsFiles as $file) {
    $path = base_path($file);
    if (file_exists($path)) {
        $size = filesize($path);
        $content = file_get_contents($path);
        
        // Check for key patterns
        $hasFaceApi = strpos($content, 'faceapi') !== false ? '✓' : '✗';
        $hasDetect = strpos($content, 'detectSingleFace') !== false ? '✓' : '✗';
        $hasSend = strpos($content, 'face_descriptor') !== false ? '✓' : '✗';
        
        echo "  $file (" . number_format($size) . " bytes)\n";
        echo "    Has faceapi: $hasFaceApi\n";
        echo "    Has detectSingleFace: $hasDetect\n";
        echo "    Has face_descriptor POST: $hasSend\n";
    } else {
        echo "  ✗ $file NOT FOUND\n";
    }
}

echo "\n6. Testing enrollment controller...\n";
$controller = new \App\Http\Controllers\Employee\FaceEnrollmentController();
echo "  FaceEnrollmentController exists: ✓\n";
echo "  saveSample method: " . (method_exists($controller, 'saveSample') ? '✓' : '✗') . "\n";
echo "  complete method: " . (method_exists($controller, 'complete') ? '✓' : '✗') . "\n";
echo "  reset method: " . (method_exists($controller, 'reset') ? '✓' : '✗') . "\n";

echo "\n=== DIAGNOSTIC COMPLETE ===\n";
