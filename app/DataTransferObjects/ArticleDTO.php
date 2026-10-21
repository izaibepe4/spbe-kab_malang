<?php

namespace App\DataTransferObjects;

use Carbon\Carbon;

class ArticleDTO
{
    public function __construct(
        public readonly string $title,
        public readonly string $slug,
        public readonly ?string $imageUrl,
        public readonly ?string $category,
        public readonly ?Carbon $date,
        public readonly ?string $excerpt = null,
        public readonly ?string $content = null,
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            slug: $data['slug'] ?? '',
            imageUrl: $data['image_url'] ?? null,
            category: $data['category'] ?? null,
            date: isset($data['date']) && $data['date'] instanceof Carbon ? $data['date'] : null,
            excerpt: $data['excerpt'] ?? null,
            content: $data['content'] ?? null,
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'image_url' => $this->imageUrl,
            'category' => $this->category,
            'date' => $this->date,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
        ];
    }

    /**
     * Get formatted date
     */
    public function getFormattedDate(string $format = 'd F Y'): ?string
    {
        return $this->date?->format($format);
    }

    /**
     * Get URL
     */
    public function getUrl(): string
    {
        return route('berita-kab-malang.show', $this->slug);
    }
}
