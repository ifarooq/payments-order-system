<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportService
{
    public function dailySettlement(?string $date = null): array
    {
        $date = $date ?? now()->toDateString();

        // Validate format YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw ValidationException::withMessages([
                'date' => ['Invalid date format, expected YYYY-MM-DD.']
            ]);
        }

        $report = DB::table('payments')
            ->selectRaw('status, COUNT(*) as count, SUM(amount) as total_amount')
            ->whereDate('created_at', $date)
            ->groupBy('status')
            ->get();

        $summary = [
            'date' => $date,
            'totals' => $report->mapWithKeys(fn ($r) => [
                $r->status => [
                    'count' => (int) $r->count,
                    'amount' => (int) $r->total_amount,
                ]
            ]),
            'overall' => [
                'count' => (int) $report->sum('count'),
                'amount' => (int) $report->sum('total_amount'),
            ]
        ];

        return $summary;
    }
}
