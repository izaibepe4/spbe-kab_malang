<?php

namespace App\Services;

use App\Models\VisitorStatistic;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VisitorCounterService
{
    /**
     * Record visitor with gender (once per session per day)
     */
    public function recordVisitor(string $gender, string $sessionId, ?string $ipAddress = null, ?string $userAgent = null): void
    {
        try {
            // Check if this session already recorded today
            $today = now()->toDateString();
            $exists = VisitorStatistic::where('session_id', $sessionId)
                ->where('visit_date', $today)
                ->exists();

            if (!$exists) {
                VisitorStatistic::create([
                    'gender' => $gender,
                    'session_id' => $sessionId,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'visit_date' => $today,
                ]);

                // Clear cache
                $this->clearCache();
            }
        } catch (\Exception $e) {
            Log::error('Error recording visitor: ' . $e->getMessage());
        }
    }

    /**
     * Get total visitor count with caching
     */
    public function getTotalVisitors(): int
    {
        return Cache::remember('visitor_count_total', 300, function () {
            try {
                return VisitorStatistic::getTotalVisitors();
            } catch (\Exception $e) {
                Log::error('Error fetching visitor count: ' . $e->getMessage());
                return 0;
            }
        });
    }

    /**
     * Get visitors by gender with caching
     */
    public function getVisitorsByGender(): array
    {
        return Cache::remember('visitor_count_by_gender', 300, function () {
            try {
                return VisitorStatistic::getVisitorsByGender();
            } catch (\Exception $e) {
                Log::error('Error fetching visitors by gender: ' . $e->getMessage());
                return ['male' => 0, 'female' => 0];
            }
        });
    }

    /**
     * Get today's visitors by gender with caching
     */
    public function getTodayVisitorsByGender(): array
    {
        return Cache::remember('visitor_count_today_by_gender', 60, function () {
            try {
                return VisitorStatistic::getTodayVisitorsByGender();
            } catch (\Exception $e) {
                Log::error('Error fetching today visitors by gender: ' . $e->getMessage());
                return ['male' => 0, 'female' => 0];
            }
        });
    }

    /**
     * Get formatted visitor count
     */
    public function getFormattedVisitors(): string
    {
        $count = $this->getTotalVisitors();
        return number_format($count, 0, ',', '.');
    }

    /**
     * Clear all visitor-related cache
     */
    public function clearCache(): void
    {
        Cache::forget('visitor_count_total');
        Cache::forget('visitor_count_by_gender');
        Cache::forget('visitor_count_today_by_gender');
    }
}
