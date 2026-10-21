<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginationHelper
{
    /**
     * Create efficient pagination from collection
     * Uses array slicing instead of loading all data
     */
    public static function paginate(
        Collection $items,
        int $perPage = 15,
        ?int $page = null,
        array $options = []
    ): LengthAwarePaginator {
        $page = $page ?: (int) request()->query('page', 1);
        $page = max($page, 1);

        $total = $items->count();
        $offset = ($page - 1) * $perPage;

        // Only slice the items we need for current page
        $currentPageItems = $items->slice($offset, $perPage)->values();

        return new LengthAwarePaginator(
            $currentPageItems,
            $total,
            $perPage,
            $page,
            array_merge($options, [
                'path' => request()->url(),
                'query' => request()->query(),
            ])
        );
    }

    /**
     * Create cursor-based pagination for better performance with large datasets
     */
    public static function cursorPaginate(
        Collection $items,
        int $perPage = 15,
        ?string $cursor = null
    ): array {
        $cursor = $cursor ?: request()->query('cursor');
        $cursorIndex = $cursor ? (int) base64_decode($cursor) : 0;

        $currentPageItems = $items->slice($cursorIndex, $perPage)->values();
        $hasMore = $items->count() > ($cursorIndex + $perPage);

        $nextCursor = $hasMore ? base64_encode((string) ($cursorIndex + $perPage)) : null;

        return [
            'data' => $currentPageItems,
            'next_cursor' => $nextCursor,
            'has_more' => $hasMore,
        ];
    }

    /**
     * Get pagination metadata
     */
    public static function getPaginationMeta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'has_more_pages' => $paginator->hasMorePages(),
        ];
    }
}
