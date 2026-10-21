<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'gender',
        'session_id',
        'ip_address',
        'user_agent',
        'visit_date',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    /**
     * Get total visitors count
     */
    public static function getTotalVisitors(): int
    {
        return self::distinct('session_id')->count();
    }

    /**
     * Get visitors count by gender
     */
    public static function getVisitorsByGender(): array
    {
        return [
            'male' => self::where('gender', 'male')->distinct('session_id')->count(),
            'female' => self::where('gender', 'female')->distinct('session_id')->count(),
        ];
    }

    /**
     * Get today's visitors count by gender
     */
    public static function getTodayVisitorsByGender(): array
    {
        $today = now()->toDateString();

        return [
            'male' => self::where('gender', 'male')
                ->where('visit_date', $today)
                ->distinct('session_id')
                ->count(),
            'female' => self::where('gender', 'female')
                ->where('visit_date', $today)
                ->distinct('session_id')
                ->count(),
        ];
    }
}
