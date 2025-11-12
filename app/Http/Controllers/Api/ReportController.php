<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function dailySettlement(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $data = $this->reportService->dailySettlement($date);
        return response()->json($data);
    }
}
