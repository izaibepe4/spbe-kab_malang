<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class HeroSlideService
{
    private string $filePath;

    private const MAX_SLIDES = 3;

    public function __construct()
    {
        $this->filePath = storage_path('app/hero_slides.json');
        $this->ensureFileExists();
    }

    /**
     * Get all slides from JSON file
     */
    public function getAll(): array
    {
        if (! file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);
        $data = json_decode($content, true);

        return $data['slides'] ?? [];
    }

    /**
     * Save slides to JSON file
     */
    public function save(array $slides): void
    {
        // Limit to max 3 slides
        $slides = array_slice($slides, 0, self::MAX_SLIDES);

        $data = ['slides' => $slides];
        file_put_contents(
            $this->filePath,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Get active slides with fallback to dummy data
     * Always returns exactly 3 slides (from JSON + dummy fallback)
     */
    public function getActiveSlides(): Collection
    {
        $jsonSlides = collect($this->getAll())
            ->values()
            ->toArray();

        $dummySlides = $this->getDummySlides();
        $result = [];

        // Fill with JSON slides first
        foreach ($jsonSlides as $index => $slide) {
            if ($index < self::MAX_SLIDES) {
                // Resolve image path
                if (isset($slide['gambar']) && ! empty($slide['gambar'])) {
                    $resolvedPath = $this->resolveImagePath($slide['gambar']);
                    $slide['gambar'] = $resolvedPath;
                } else {
                    // If no image, use default
                    $slide['gambar'] = asset('assets/img/hero_section.png');
                }
                $result[] = $slide;
            }
        }

        // Fill remaining slots with dummy slides
        $dummyIndex = 0;
        while (count($result) < self::MAX_SLIDES && $dummyIndex < count($dummySlides)) {
            $result[] = $dummySlides[$dummyIndex];
            $dummyIndex++;
        }

        return collect($result)->take(self::MAX_SLIDES)->values();
    }

    /**
     * Get dummy/fallback slides
     */
    private function getDummySlides(): array
    {
        $heroImage = asset('assets/img/hero_section.png');

        return [
            [
                'judul' => 'Sistem Pemerintahan Berbasis Elektronik',
                'subjudul' => 'Menuju Pemerintahan Digital Kabupaten Malang',
                'alignment' => 'left',
                'button_style' => 'orange-navy',
                'teks_button' => 'Explore',
                'button_link' => '#definisi',
                'button_icon' => 'arrow',
                'gambar' => $heroImage,
            ],
            [
                'judul' => 'Meningkatkan Efisiensi dan Transparansi',
                'subjudul' => 'Transformasi Digital untuk Pelayanan Publik',
                'alignment' => 'center',
                'button_style' => 'navy-orange',
                'teks_button' => 'Pelajari Lebih Lanjut',
                'button_link' => '#definisi',
                'button_icon' => 'info',
                'gambar' => $heroImage,
            ],
            [
                'judul' => 'Kabupaten Malang yang Lebih Digital',
                'subjudul' => 'Inovasi Teknologi untuk Masa Depan',
                'alignment' => 'right',
                'button_style' => 'navy-orange',
                'teks_button' => 'Mulai Jelajahi',
                'button_link' => '#definisi',
                'button_icon' => 'play',
                'gambar' => $heroImage,
            ],
        ];
    }

    /**
     * Resolve image path (handle both storage paths and asset paths)
     */
    public function resolveImagePath(string $path): string
    {
        // If it's already a full URL, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        // If it starts with /storage/, convert to asset URL
        if (str_starts_with($path, '/storage/')) {
            return asset($path);
        }

        // If it starts with /, it's already a full path
        if (str_starts_with($path, '/')) {
            return $path;
        }

        // If it's a storage path (from FileUpload), convert to URL
        // FileUpload stores relative to storage/app/public, e.g., "hero/filename.jpg"
        if (str_starts_with($path, 'hero/')) {
            // Use asset() instead of Storage::url() for better compatibility
            return asset('storage/'.$path);
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        // If it's an asset path (like "assets/img/hero_section.png"), use asset()
        if (str_starts_with($path, 'assets/')) {
            return asset($path);
        }

        // Default: try as storage path first
        if (Storage::disk('public')->exists($path)) {
            return asset('storage/'.$path);
        }

        // Last resort: try as asset
        return asset($path);
    }

    /**
     * Ensure JSON file exists
     */
    private function ensureFileExists(): void
    {
        if (! file_exists($this->filePath)) {
            $this->save([]);
        }
    }

    /**
     * Get default slide structure
     */
    public function getDefaultSlide(): array
    {
        return [
            'judul' => '',
            'subjudul' => '',
            'alignment' => 'left',
            'button_style' => 'navy-orange',
            'teks_button' => '',
            'button_link' => '',
            'button_icon' => 'arrow',
            'gambar' => '',
        ];
    }
}
