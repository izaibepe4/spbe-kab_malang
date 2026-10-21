<?php

namespace App\Filament\Pages;

use App\Services\HeroSlideService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ManageHeroSlides extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static string $view = 'filament.pages.manage-hero-slides';

    protected static ?string $navigationLabel = 'Kelola Hero Section';

    protected static ?string $title = 'Kelola Hero Section';

    protected static ?string $navigationGroup = 'Konten Website';

    protected static ?int $navigationSort = 10;

    public array $slides = [];

    public function mount(): void
    {
        $service = app(HeroSlideService::class);
        $jsonSlides = $service->getAll();

        // Load slides from JSON, or start with empty array (user can add one by one)
        if (empty($jsonSlides)) {
            $this->slides = []; // Start empty, user can add slides one by one
        } else {
            // Load existing slides (max 3)
            $this->slides = array_slice($jsonSlides, 0, 3);
        }

        $this->form->fill(['slides' => $this->slides]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Repeater::make('slides')
                    ->label('Hero Slides')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subjudul')
                            ->label('Subjudul')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('Teks kecil di atas judul (opsional)'),

                        Forms\Components\Radio::make('alignment')
                            ->label('Posisi Konten')
                            ->options([
                                'left' => 'Kiri',
                                'center' => 'Tengah',
                                'right' => 'Kanan',
                            ])
                            ->default('left')
                            ->required()
                            ->inline()
                            ->columnSpan(1),

                        Forms\Components\Select::make('button_style')
                            ->label('Style Tombol')
                            ->options([
                                'navy-orange' => 'Biru → Orange (Hover)',
                                'orange-navy' => 'Orange → Biru (Hover)',
                            ])
                            ->default('navy-orange')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('teks_button')
                            ->label('Teks Tombol')
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->helperText('Kosongkan jika tidak ingin menampilkan tombol'),

                        Forms\Components\TextInput::make('button_link')
                            ->label('Link Tombol')
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->helperText('Contoh: #definisi atau https://example.com'),

                        Forms\Components\Select::make('button_icon')
                            ->label('Icon Tombol')
                            ->options([
                                'arrow' => 'Panah (→)',
                                'info' => 'Info (i)',
                                'play' => 'Play (▶)',
                                'none' => 'Tidak ada icon',
                            ])
                            ->default('arrow')
                            ->columnSpan(1),

                        Forms\Components\FileUpload::make('gambar')
                            ->label('Gambar Background')
                            ->directory('hero')
                            ->disk('public')
                            ->acceptedFileTypes(['image/*'])
                            ->maxSize(5120) // 5MB
                            ->imagePreviewHeight('200')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Gambar background untuk hero slide. Rekomendasi: 1920x1080px, maksimal 5MB'),

                        Forms\Components\TextInput::make('urutan')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('aktif')
                            ->label('Aktif')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->defaultItems(0) // Start with 0 items, user can add one by one
                    ->minItems(0) // Allow empty (will use dummy fallback)
                    ->maxItems(3) // Maximum 3 slides
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => $state['judul'] ?: 'Slide Baru')
                    ->reorderable()
                    ->addActionLabel('Tambah Slide')
                    ->deleteAction(
                        fn ($action) => $action->label('Hapus Slide')
                    )
                    ->helperText('Maksimal 3 slide. Jika kurang dari 3, akan menggunakan slide dummy sebagai fallback.')
                    ->columnSpanFull(),
            ])
            ->statePath('slides');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Process file uploads - convert to storage path and resize/crop
        foreach ($data['slides'] as &$slide) {
            if (isset($slide['gambar'])) {
                if (is_array($slide['gambar'])) {
                    // FileUpload returns array, get first item
                    $imagePath = $slide['gambar'][0] ?? '';
                    if ($imagePath) {
                        // Process and resize image (this modifies the file and returns the path)
                        $processedPath = $this->processHeroImage($imagePath);
                        // Store relative path (e.g., "hero/filename.jpg")
                        $slide['gambar'] = $processedPath;
                    } else {
                        // No image uploaded, use default
                        $slide['gambar'] = 'assets/img/hero_section.png';
                    }
                } elseif (is_string($slide['gambar']) && ! empty($slide['gambar'])) {
                    // Check if it's a new upload (starts with hero/)
                    if (str_starts_with($slide['gambar'], 'hero/')) {
                        // Process new upload
                        $processedPath = $this->processHeroImage($slide['gambar']);
                        $slide['gambar'] = $processedPath;
                    } elseif (str_starts_with($slide['gambar'], 'assets/')) {
                        // Already default asset path, keep it
                        $slide['gambar'] = $slide['gambar'];
                    } else {
                        // Unknown format, try to process or keep as is
                        $slide['gambar'] = $slide['gambar'];
                    }
                } else {
                    // No image, use default
                    $slide['gambar'] = 'assets/img/hero_section.png';
                }
            } else {
                // No gambar field, use default
                $slide['gambar'] = 'assets/img/hero_section.png';
            }
        }

        $service = app(HeroSlideService::class);
        $service->save($data['slides']);

        Notification::make()
            ->title('Hero slides berhasil disimpan')
            ->success()
            ->send();

        // Reload form
        $this->mount();
    }

    /**
     * Process hero image: resize and crop to 1920x1080 (16:9 aspect ratio)
     */
    private function processHeroImage(string $imagePath): string
    {
        try {
            // Handle different path formats
            $fullPath = null;

            // If path starts with hero/, it's relative to storage/app/public
            if (str_starts_with($imagePath, 'hero/')) {
                $fullPath = Storage::disk('public')->path($imagePath);
            } else {
                // Try as is
                $fullPath = Storage::disk('public')->path($imagePath);
            }

            if (! file_exists($fullPath)) {
                \Log::warning('Hero image file not found', ['path' => $fullPath, 'original' => $imagePath]);

                return $imagePath; // Return original if file doesn't exist
            }

            // Hero section dimensions: 1920x1080 (16:9 aspect ratio)
            $targetWidth = 1920;
            $targetHeight = 1080;

            // Use GD to resize and crop
            $this->resizeAndCropImage($fullPath, $targetWidth, $targetHeight);

            // Return the original path format (relative to storage/app/public)
            return $imagePath;
        } catch (\Exception $e) {
            // If processing fails, return original path
            \Log::warning('Failed to process hero image: '.$e->getMessage(), [
                'path' => $imagePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $imagePath;
        }
    }

    /**
     * Resize and crop image using GD (fallback method)
     */
    private function resizeAndCropImage(string $filePath, int $targetWidth, int $targetHeight): void
    {
        $imageInfo = getimagesize($filePath);
        if (! $imageInfo) {
            return;
        }

        $sourceWidth = $imageInfo[0];
        $sourceHeight = $imageInfo[1];
        $mimeType = $imageInfo['mime'];

        // Create source image based on type
        switch ($mimeType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($filePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($filePath);
                break;
            case 'image/webp':
                $sourceImage = imagecreatefromwebp($filePath);
                break;
            default:
                return;
        }

        if (! $sourceImage) {
            return;
        }

        // Calculate aspect ratios
        $sourceAspect = $sourceWidth / $sourceHeight;
        $targetAspect = $targetWidth / $targetHeight;

        // Calculate crop dimensions
        if ($sourceAspect > $targetAspect) {
            // Source is wider - crop width
            $cropHeight = $sourceHeight;
            $cropWidth = $sourceHeight * $targetAspect;
            $cropX = ($sourceWidth - $cropWidth) / 2;
            $cropY = 0;
        } else {
            // Source is taller - crop height
            $cropWidth = $sourceWidth;
            $cropHeight = $sourceWidth / $targetAspect;
            $cropX = 0;
            $cropY = ($sourceHeight - $cropHeight) / 2;
        }

        // Create target image
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        // Preserve transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($targetImage, false);
            imagesavealpha($targetImage, true);
            $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
            imagefill($targetImage, 0, 0, $transparent);
        }

        // Resize and crop
        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0, 0,
            $cropX, $cropY,
            $targetWidth, $targetHeight,
            $cropWidth, $cropHeight
        );

        // Save based on original type
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($targetImage, $filePath, 90);
                break;
            case 'image/png':
                imagepng($targetImage, $filePath, 9);
                break;
            case 'image/gif':
                imagegif($targetImage, $filePath);
                break;
            case 'image/webp':
                imagewebp($targetImage, $filePath, 90);
                break;
        }

        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($targetImage);
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }
}
