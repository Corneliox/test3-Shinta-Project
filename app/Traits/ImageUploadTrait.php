<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

trait ImageUploadTrait
{
    /**
     * Handle Image Upload: Saves Optimized (Default) and Original (Backup)
     */
    public function uploadImage($file, $directory = 'artworks')
    {
        // 1. Generate Unique Filename
        $extension = $file->getClientOriginalExtension();
        $basename = uniqid() . '_' . time();
        $filename = $basename . '.' . $extension;
        $originalFilename = $basename . '_original.' . $extension;

        // 2. Save the HIGH RES version ( _original )
        Storage::disk('public')->putFileAs($directory, $file, $originalFilename);

        // 3. Process & Save the OPTIMIZED version (Standard Name)
        $this->resizeAndSave($file, $directory . '/' . $filename);

        // Return the path to the OPTIMIZED version (so DB uses this by default)
        return $directory . '/' . $filename;
    }

    /**
     * Handle Image from Temp Path (For your Google Drive Pull feature)
     */
    public function processTempImage($tempPath, $targetDirectory = 'artworks')
    {
        if (!Storage::disk('public')->exists($tempPath)) return null;

        $fullTempPath = Storage::disk('public')->path($tempPath);
        $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
        $basename = uniqid() . '_' . time();
        
        $filename = $basename . '.' . $extension;
        $originalFilename = $basename . '_original.' . $extension;

        // 1. Move Temp to "Original"
        Storage::disk('public')->move($tempPath, $targetDirectory . '/' . $originalFilename);

        // 2. Create Optimized Version from that Original
        $sourcePath = Storage::disk('public')->path($targetDirectory . '/' . $originalFilename);
        
        // Mock an UploadedFile to reuse the resizer
        $this->resizeAndSave($sourcePath, $targetDirectory . '/' . $filename, true);

        return $targetDirectory . '/' . $filename;
    }

    /**
     * Native PHP Resizer (No Composer Packages needed)
     */
    private function resizeAndSave($source, $targetPath, $isPath = false)
    {
        // Get source path
        $srcPath = $isPath ? $source : $source->getRealPath();
        
        // Get dimensions and type
        list($width, $height, $type) = getimagesize($srcPath);
        
        // Calculate new dimensions (Max Width 1200px - keeps aspect ratio)
        $maxWidth = 1200; 
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($height / $width) * $newWidth;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Create resource from source
        $imageResource = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($srcPath),
            IMAGETYPE_PNG => imagecreatefrompng($srcPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($srcPath),
            default => null,
        };

        if (!$imageResource) {
            // Fallback: If type not supported, just copy the file
            Storage::disk('public')->copy(
                str_replace(storage_path('app/public/'), '', $srcPath), // fix path for copy
                $targetPath
            );
            return;
        }

        // Create new empty image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve Transparency for PNG/WEBP
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        // Resize
        imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save to Storage Path
        $fullDestPath = Storage::disk('public')->path($targetPath);
        
        // Ensure directory exists
        $dir = dirname($fullDestPath);
        if (!file_exists($dir)) mkdir($dir, 0755, true);

        // Save with 75% Quality (Good balance for <1MB)
        switch ($type) {
            case IMAGETYPE_JPEG: imagejpeg($newImage, $fullDestPath, 75); break;
            case IMAGETYPE_PNG: imagepng($newImage, $fullDestPath, 8); break; // 0-9 compression
            case IMAGETYPE_WEBP: imagewebp($newImage, $fullDestPath, 75); break;
        }

        // Cleanup memory
        imagedestroy($newImage);
        imagedestroy($imageResource);
    }
}