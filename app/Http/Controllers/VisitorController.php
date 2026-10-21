<?php

namespace App\Http\Controllers;

use App\Services\VisitorCounterService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VisitorController extends Controller
{
    protected VisitorCounterService $visitorService;

    public function __construct(VisitorCounterService $visitorService)
    {
        $this->visitorService = $visitorService;
    }

    /**
     * Record visitor gender selection (once per session)
     */
    public function recordGender(Request $request): JsonResponse
    {
        $request->validate([
            'gender' => 'required|in:male,female'
        ]);

        $sessionId = session()->getId();
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        $this->visitorService->recordVisitor(
            $request->gender,
            $sessionId,
            $ipAddress,
            $userAgent
        );

        // Mark session as recorded so popup won't show again
        session(['visitor_gender_recorded' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Gender recorded successfully'
        ]);
    }

    /**
     * Check if visitor gender already recorded in this session
     */
    public function checkGenderRecorded(): JsonResponse
    {
        $recorded = session('visitor_gender_recorded', false);

        return response()->json([
            'recorded' => $recorded
        ]);
    }

    /**
     * Clear session for testing purposes
     */
    public function clearSession(): JsonResponse
    {
        session()->forget('visitor_gender_recorded');

        return response()->json([
            'success' => true,
            'message' => 'Session cleared'
        ]);
    }
}
