<?php

namespace App\Helpers;

class InputSanitizer
{
    /**
     * Sanitize string input
     */
    public static function sanitizeString(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        // Remove HTML tags
        $input = strip_tags($input);

        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');

        // Trim whitespace
        $input = trim($input);

        return $input;
    }

    /**
     * Sanitize search query
     */
    public static function sanitizeSearchQuery(?string $query): ?string
    {
        if ($query === null) {
            return null;
        }

        // Remove HTML tags
        $query = strip_tags($query);

        // Remove special characters that could be used for SQL injection
        $query = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $query);

        // Trim and normalize whitespace
        $query = preg_replace('/\s+/', ' ', trim($query));

        // Limit length
        $query = mb_substr($query, 0, 200);

        return $query;
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail(?string $email): ?string
    {
        if ($email === null) {
            return null;
        }

        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize URL
     */
    public static function sanitizeUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize integer
     */
    public static function sanitizeInt(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $sanitized = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        if ($sanitized === false || $sanitized === '') {
            return null;
        }

        return (int) $sanitized;
    }

    /**
     * Sanitize array of strings
     */
    public static function sanitizeArray(array $input): array
    {
        return array_map(fn ($item) => self::sanitizeString($item), $input);
    }

    /**
     * Remove XSS attempts
     */
    public static function removeXss(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        // Remove null bytes
        $input = str_replace(chr(0), '', $input);

        // Remove script tags
        $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);

        // Remove event handlers
        $input = preg_replace('/\bon\w+\s*=\s*["\']?[^"\']*["\']?/i', '', $input);

        // Remove javascript: protocol
        $input = preg_replace('/javascript:/i', '', $input);

        return $input;
    }
}
