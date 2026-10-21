<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Get the search query
     */
    public function getSearchQuery(): string
    {
        return trim($this->input('q') ?? $this->input('search', ''));
    }

    /**
     * Get the limit
     */
    public function getLimit(): int
    {
        return min((int) $this->input('limit', 10), 50);
    }

    /**
     * Sanitize search query
     */
    public function sanitizedQuery(): string
    {
        $query = $this->getSearchQuery();

        // Remove special characters yang berbahaya
        $query = preg_replace('/[<>"\']/', '', $query);

        // Remove multiple spaces
        $query = preg_replace('/\s+/', ' ', $query);

        return trim($query);
    }
}
