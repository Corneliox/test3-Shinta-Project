<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Event;
// use App\Models\HeroImage; // Assuming you have this model
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerController extends Controller
{
    public function run()
    {
        // Increase limits for this heavy script
        ini_set('max_execution_time', 600); // Increased to 10 minutes
        ini_set('memory_limit', '512M');

        $log = [];
        $count = 0;

        $log[] = "Starting stricter optimization (Max 1024px, Q65)...";

        // 1. Optimize Artworks
        $artworks = Artwork::all();
        foreach ($artworks as $art) {
            if ($this->optimizeFile($art->image_path)) {
                $log[] = "Processed Artwork: " . ($art->title ?? $art->id);
                $count++;
            }
        }

        // 2. Optimize Events
        $events = Event::all();
        foreach ($events as $event) {
            if ($this->optimizeFile($event->image_path)) {
                $log[] = "Processed Event: " . ($event->title ?? $event->id);
                $count++;
            }
        }

        // 3. Optimize Hero Images (if applicable)
        if (class_exists('App\Models\HeroImage')) {
            // $heroes = \App\Models\HeroImage::all(); // Adjust namespace if needed
            // foreach ($heroes as $hero) {
            //     if ($this->optimizeFile($hero->image_path)) {
            //         $log[] = "Processed Hero Image ID: " . $hero->id;
            //         $count++;
            //     }
            // }
        }

        return "<h1>Optimization Complete!</h1><p>Processed $count images with stricter settings.</p><pre style='background:#f4f4f4;padding:15px;max-height:400px;overflow:auto;'>" . implode("\n", $log) . "</pre>";
    }

    /**
     * The Logic: Rename current to _original, then resize back to current name.
     */
    private function optimizeFile($filePath)
    {
        if (!$filePath) return false;

        $fullPath = Storage::disk('public')->path($filePath);
        $info = pathinfo($fullPath);
        
        $originalName = $info['filename'] . '_original.' . $info['extension'];
        // Need relative path for Storage facade
        $relativePathDir = dirname($filePath);
        $originalPathStorage = $relativePathDir . '/' . $originalName;

        // Determine SOURCE and DESTINATION
        if (Storage::disk('public')->exists($originalPathStorage)) {
            // CASE A: Re-optimizing. Source is the existing backup.
            // We overwrite the current optimized file with a smaller version.
            $sourcePath = Storage::disk('public')->path($originalPathStorage);
        } else {
            // CASE B: First time optimizing.
            if (!Storage::disk('public')->exists($filePath)) return false;

            // 1. Move current BIG file to _original backup
            Storage::disk('public')->move($filePath, $originalPathStorage);
            // Source is now that backup
            $sourcePath = Storage::disk('public')->path($originalPathStorage);
        }

        // Destination is always the current live path
        $destPath = Storage::disk('public')->path($filePath);

        // Apply tighter resizing
        return $this->resizeImage($sourcePath, $destPath);
    }

    private function resizeImage($source, $destination)
    {
        if (!file_exists($source)) return false;

        list($width, $height, $type) = getimagesize($source);
        
        // --- UPDATED SETTING: Max 1024px width ---
        $maxWidth = 1024;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = ($height / $width) * $newWidth;
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $imageResource = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($source),
            IMAGETYPE_PNG => imagecreatefrompng($source),
            IMAGETYPE_WEBP => imagecreatefromwebp($source),
            default => null,
        };

        if (!$imageResource) {
            // If we can't read the original backup, and the destination doesn't exist, 
            // restore the backup to destination to prevent broken links.
            if (!file_exists($destination)) {
                 copy($source, $destination);
            }
            return false;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Transparency for PNG/WEBP
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        // High quality resampling
        imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save Optimized with TIGHTER Compression
        switch ($type) {
            // --- UPDATED SETTING: 65% Quality ---
            case IMAGETYPE_JPEG: imagejpeg($newImage, $destination, 65); break; 
            case IMAGETYPE_PNG: imagepng($newImage, $destination, 8); break;
            // --- UPDATED SETTING: 65% Quality ---
            case IMAGETYPE_WEBP: imagewebp($newImage, $destination, 65); break;
        }

        imagedestroy($newImage);
        imagedestroy($imageResource);

        clearstatcache(); // Clear cache to ensure file size is read correctly if checked immediately
        return true;
    }
}