<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\Event;
use App\Models\HeroImage; // Assuming you have this model
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerController extends Controller
{
    public function run()
    {
        // Increase limits for this heavy script
        ini_set('max_execution_time', 300); // 5 minutes
        ini_set('memory_limit', '512M');

        $log = [];
        $count = 0;

        // 1. Optimize Artworks
        $artworks = Artwork::all();
        foreach ($artworks as $art) {
            if ($this->optimizeFile($art->image_path)) {
                $log[] = "Optimized Artwork: " . $art->title;
                $count++;
            }
        }

        // 2. Optimize Events
        $events = Event::all();
        foreach ($events as $event) {
            if ($this->optimizeFile($event->image_path)) {
                $log[] = "Optimized Event: " . $event->title;
                $count++;
            }
        }

        // 3. Optimize Hero Images (if applicable)
        // Adjust this loop if your HeroImage model works differently
        if (class_exists('App\Models\HeroImage')) {
            $heroes = HeroImage::all();
            foreach ($heroes as $hero) {
                if ($this->optimizeFile($hero->image_path)) {
                    $log[] = "Optimized Hero Image ID: " . $hero->id;
                    $count++;
                }
            }
        }

        return "<h1>Optimization Complete!</h1><p>Processed $count images.</p><pre>" . implode("\n", $log) . "</pre>";
    }

    /**
     * The Logic: Rename current to _original, then resize back to current name.
     */
    private function optimizeFile($filePath)
    {
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            return false; // File doesn't exist
        }

        $fullPath = Storage::disk('public')->path($filePath);
        $info = pathinfo($fullPath);
        
        // Skip if it looks like we already optimized it (check for _original existence)
        $originalName = $info['filename'] . '_original.' . $info['extension'];
        $originalPathRel = $info['dirname'] . '/' . $originalName;
        
        // FIX: The pathinfo returns absolute path for dirname, we need relative for Storage
        $relativePathDir = dirname($filePath);
        $originalPathStorage = $relativePathDir . '/' . $originalName;

        if (Storage::disk('public')->exists($originalPathStorage)) {
            return false; // Already has a backup, assume optimized
        }

        // 1. Rename current BIG file to _original
        Storage::disk('public')->move($filePath, $originalPathStorage);

        // 2. Create new optimized version at the OLD path (so database links still work)
        // We use the absolute path of the moved original as source
        $sourcePath = Storage::disk('public')->path($originalPathStorage);
        $destPath = Storage::disk('public')->path($filePath);

        return $this->resizeImage($sourcePath, $destPath);
    }

    private function resizeImage($source, $destination)
    {
        list($width, $height, $type) = getimagesize($source);
        
        // Target: Max 1200px width
        $maxWidth = 1200;
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
            // Can't process? Just copy it back so site doesn't break
            copy($source, $destination);
            return false;
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Transparency for PNG/WEBP
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $imageResource, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save Optimized
        switch ($type) {
            case IMAGETYPE_JPEG: imagejpeg($newImage, $destination, 75); break; // 75% Quality
            case IMAGETYPE_PNG: imagepng($newImage, $destination, 8); break;
            case IMAGETYPE_WEBP: imagewebp($newImage, $destination, 75); break;
        }

        imagedestroy($newImage);
        imagedestroy($imageResource);

        return true;
    }
}