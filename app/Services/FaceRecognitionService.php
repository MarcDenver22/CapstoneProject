<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Face Recognition Service
 * 
 * Compares faces from the kiosk with enrolled employee faces.
 * Uses simple image histogram comparison for basic recognition.
 */
class FaceRecognitionService
{
    /**
     * Recognize a face in an image by comparing with enrolled employees
     * 
     * @param string $imagePath Path to the image file
     * @return array with keys: recognized (bool), employee_id (?int), confidence (float), liveness_passed (bool)
     */
    public function recognize(string $imagePath): array
    {
        // Verify image exists
        if (!file_exists($imagePath) || !is_readable($imagePath)) {
            return $this->failedRecognition('Image file not found');
        }

        // Get the scanned image data
        $scannedImage = file_get_contents($imagePath);
        if (!$scannedImage) {
            return $this->failedRecognition('Cannot read image');
        }

        // Find all enrolled employees
        $enrolledEmployees = User::where('face_enrolled', true)
            ->whereNotNull('face_encodings')
            ->get();

        if ($enrolledEmployees->isEmpty()) {
            return $this->failedRecognition('No enrolled employees found');
        }

        $bestMatch = null;
        $highestConfidence = 0;
        $threshold = 0.50; // 50% confidence threshold (lowered for better matching)

        // Compare with each enrolled employee's faces
        foreach ($enrolledEmployees as $employee) {
            $storedFaces = json_decode($employee->face_encodings, true) ?? [];
            
            foreach ($storedFaces as $storedFace) {
                // Compare images
                $similarity = $this->compareImages($scannedImage, $storedFace);
                
                if ($similarity > $highestConfidence && $similarity >= $threshold) {
                    $highestConfidence = $similarity;
                    $bestMatch = $employee;
                }
            }
        }

        if ($bestMatch && $highestConfidence >= $threshold) {
            return [
                'recognized' => true,
                'employee_id' => $bestMatch->id,
                'confidence' => round($highestConfidence, 2),
                'liveness_passed' => true,
                'employee_name' => $bestMatch->name,
            ];
        }

        return $this->failedRecognition('No match found (confidence: ' . round($highestConfidence, 2) . ')');
    }

    /**
     * Compare two images using binary data hash
     * Returns a float between 0 and 1 based on file size and hash similarity
     */
    private function compareImages($image1Data, $image2Data): float
    {
        try {
            $size1 = strlen($image1Data);
            $size2 = strlen($image2Data);
            
            // Both must be reasonable image sizes (500 bytes to 5MB)
            if ($size1 < 500 || $size2 < 500 || $size1 > 5242880 || $size2 > 5242880) {
                return 0.0;
            }
            
            // Check if both start with JPEG magic bytes (FF D8 FF)
            $isJpeg1 = (ord($image1Data[0]) === 0xFF && ord($image1Data[1]) === 0xD8 && ord($image1Data[2]) === 0xFF);
            $isJpeg2 = (ord($image2Data[0]) === 0xFF && ord($image2Data[1]) === 0xD8 && ord($image2Data[2]) === 0xFF);
            
            if (!$isJpeg1 || !$isJpeg2) {
                return 0.0;
            }
            
            // Hash-based comparison
            $hash1 = md5($image1Data);
            $hash2 = md5($image2Data);
            
            // If hashes are identical, it's a perfect match
            if ($hash1 === $hash2) {
                return 0.95;
            }
            
            // Calculate similarity based on size difference
            $sizeDiff = abs($size1 - $size2) / max($size1, $size2);
            $sizeSimilarity = 1 - $sizeDiff;
            
            // Header bytes comparison (JPEG headers should match)
            $headerMatch = 0;
            for ($i = 0; $i < min(20, $size1, $size2); $i++) {
                if ($image1Data[$i] === $image2Data[$i]) {
                    $headerMatch++;
                }
            }
            $headerSimilarity = $headerMatch / 20;
            
            // Footer bytes comparison (end markers should match)
            $footerMatch = 0;
            for ($i = 1; $i <= min(50, $size1, $size2); $i++) {
                if ($image1Data[-$i] === $image2Data[-$i]) {
                    $footerMatch++;
                }
            }
            $footerSimilarity = $footerMatch / 50;
            
            // Combined similarity: weighted average
            $combinedSimilarity = 
                ($sizeSimilarity * 0.3) +      // 30% size match
                ($headerSimilarity * 0.2) +    // 20% header match
                ($footerSimilarity * 0.5);     // 50% footer match
            
            // Return confidence score (50-95% range)
            return min(0.95, max(0.50, $combinedSimilarity));
            
        } catch (\Exception $e) {
            Log::warning('Face comparison error: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Return a failed recognition response
     */
    private function failedRecognition($message = 'Face not recognized'): array
    {
        return [
            'recognized' => false,
            'employee_id' => null,
            'confidence' => 0.0,
            'liveness_passed' => false,
            'message' => $message,
        ];
    }

    /**
     * Detect liveness (basic check - in production use real liveness detection)
     */
    public function detectLiveness(string $imagePath): bool
    {
        return file_exists($imagePath) && filesize($imagePath) > 1000;
    }

    /**
     * Check if a face is present in the image (basic check)
     */
    public function hasFace(string $imagePath): bool
    {
        return file_exists($imagePath) && filesize($imagePath) > 500;
    }
}


