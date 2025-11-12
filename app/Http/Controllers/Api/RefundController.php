<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RefundController extends Controller
{
    public function __construct(private RefundService $refundService) {}

    public function processRefund(Request $request): JsonResponse
    {
        $idempotencyKey = $request->header('Idempotency-Key');

        $data = $request->validate([
            'payment_id' => 'required|integer|exists:payments,id',
            'amount'     => 'required|integer|min:1',
        ]);

        $refund = $this->refundService->processRefund($data, $idempotencyKey);
        return response()->json($refund, 201);
    }
}
