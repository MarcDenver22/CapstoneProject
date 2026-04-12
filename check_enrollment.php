<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::all();
echo "Employee ID | Name | Enrolled | Samples | Has Encodings\n";
echo str_repeat("-", 70) . "\n";

foreach($users as $u) {
    $enrolled = $u->face_enrolled ? 'YES' : 'NO';
    $sampleCount = $u->face_samples_count;
    $hasEncodings = strlen($u->face_encodings) > 0 ? 'YES' : 'NO';
    
    echo sprintf("%-12s | %-20s | %-8s | %-7s | %s\n", 
        $u->employee_id ?? 'NULL', 
        $u->name, 
        $enrolled, 
        $sampleCount,
        $hasEncodings
    );
    
    // Show first descriptor sample if enrolled
    if ($u->face_enrolled && $u->face_encodings) {
        $descriptors = json_decode($u->face_encodings, true);
        echo "  Descriptor type: " . gettype($descriptors) . "\n";
        echo "  Raw data (first 100 chars): " . substr($u->face_encodings, 0, 100) . "\n";
        if (is_array($descriptors) && count($descriptors) > 0) {
            if (is_array($descriptors[0])) {
                echo "  First descriptor (first 5 values): " . json_encode(array_slice($descriptors[0], 0, 5)) . "\n";
            } else {
                echo "  First descriptor is not an array, it's a: " . gettype($descriptors[0]) . "\n";
            }
        }
    }
}
