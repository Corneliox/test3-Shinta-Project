<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

trait ImageUploadTrait
{
    /**
     * Handle Image Upload: Saves Optimized (Default) and Original (Backup)
     * Updated: Now accepts $rotation parameter
     */
    public function uploadImage($file, $directory = 'artworks', $rotation = 0)
    {
        $extension = $file->getClientOriginalExtension();
        $basename = uniqid() . '_' . time();
        $filename = $basename . '.' . $extension;
        $originalFilename = $basename . '_original.' . $extension;

        // 1. Process & Save the HIGH RES version (Rotated if needed)
        // We pass 'true' for $isOriginal to skip heavy resizing but apply rotation
        $this->resizeAndSave($file, $directory . '/' . $originalFilename, false, $rotation, true);

        // 2. Process & Save the OPTIMIZED version (Standard Name)
        // We use the NEWLY saved original as source to ensure rotation is applied to the small one too
        $sourcePath = Storage::disk('public')->path($directory . '/' . $originalFilename);
        $this->resizeAndSave($sourcePath, $directory . '/' . $filename, true, 0, false); 
        // Note: Rotation is 0 here because the source (original) is already rotated.

        return $directory . '/' . $filename;
    }

    /**
     * Handle Image from Temp Path (For your Google Drive Pull feature)
     */
    public function processTempImage($tempPath, $targetDirectory = 'artworks', $rotation = 0)
    {
        if (!Storage::disk('public')->exists($tempPath)) return null;

        $fullTempPath = Storage::disk('public')->path($tempPath);
        $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
        $basename = uniqid() . '_' . time();
        
        $filename = $basename . '.' . $extension;
        $originalFilename = $basename . '_original.' . $extension;

        // 1. Move/Process Original (Apply Rotation here)
        $this->resizeAndSave($fullTempPath, $targetDirectory . '/' . $originalFilename, true, $rotation, true);

        // 2. Create Optimized Version from that Original
        $sourcePath = Storage::disk('public')->path($targetDirectory . '/' . $originalFilename);
        $this->resizeAndSave($sourcePath, $targetDirectory . '/' . $filename, true, 0, false);

        // Cleanup temp
        if(Storage::disk('public')->exists($tempPath)) Storage::disk('public')->delete($tempPath);

        return $targetDirectory . '/' . $filename;
    }

    /**
     * Native PHP Resizer with Rotation Support
     */
    private function resizeAndSave($source, $targetPath, $isPath = false, $rotation = 0, $isHighRes = false)
    {
        // Get source path
        $srcPath = $isPath ? $source : $source->getRealPath();
        
        // Get dimensions and type
        list($width, $height, $type) = getimagesize($srcPath);
        
        // Create resource from source
        $imageResource = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($srcPath),
            IMAGETYPE_PNG => imagecreatefrompng($srcPath),
            IMAGETYPE_WEBP => imagecreatefromwebp($srcPath),
            default => null,
        };

        if (!$imageResource) {
            // Fallback: Just copy if type not supported
            if ($isPath && !file_exists($targetPath)) {
                 copy($srcPath, Storage::disk('public')->path($targetPath));
            }
            return;
        }

        // --- 1. APPLY ROTATION ---
        if ($rotation != 0) {
            // PHP rotates Counter Clockwise. User wants Clockwise.
            // 90 (Right) -> -90 (PHP)
            $imageResource = imagerotate($imageResource, -1 * $rotation, 0);
            
            // Update dimensions after rotation
            $width = imagesx($imageResource);
            $height = imagesy($imageResource);
        }

        // --- 2. CALCULATE NEW DIMENSIONS ---
        // If High Res, keep original size (unless massive > 3000). If Optimized, cap at 1200.
        $maxWidth = $isHighRes ? 3000 : 1200; 

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($height / $width) * $newWidth;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Create new canvas
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Handle Transparency for PNG/WEBP
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

        // Save with Appropriate Quality
        $quality = $isHighRes ? 90 : 75; 

        switch ($type) {
            case IMAGETYPE_JPEG: imagejpeg($newImage, $fullDestPath, $quality); break;
            case IMAGETYPE_PNG: imagepng($newImage, $fullDestPath, 8); break; // 0-9 compression
            case IMAGETYPE_WEBP: imagewebp($newImage, $fullDestPath, $quality); break;
        }

        // Cleanup memory
        imagedestroy($newImage);
        imagedestroy($imageResource);
    }
}