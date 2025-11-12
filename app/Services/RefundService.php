<?php

namespace App\Services;

use App\Repositories\RefundRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RefundService
{
    public function __construct(
        private RefundRepository $refundRepo,
        private PaymentRepository $paymentRepo
    ) {}

    public function processRefund(array $data, ?string $idempotencyKey = null)
    {
        try {
            $cacheKey = $idempotencyKey ? "refund:{$idempotencyKey}" : null;

            // Check Redis for idempotency
            if ($cacheKey && ($existing = Redis::get($cacheKey))) {
                return json_decode($existing, true);
            }

            $refund = DB::transaction(function () use ($data) {
                $payment = $this->paymentRepo->findById($data['payment_id']);

                if (strtolower($payment->status) !== 'captured') {
                    throw ValidationException::withMessages([
                        'payment' => ['Only captured payments can be refunded.'],
                    ]);
                }

                if ($data['amount'] > $payment->amount) {
                    throw ValidationException::withMessages([
                        'amount' => ['Refund amount cannot exceed payment amount.'],
                    ]);
                }

                // Create refund record
                $refund = $this->refundRepo->create([
                    'payment_id' => $payment->id,
                    'amount'     => $data['amount'],
                    'status'     => 'completed',
                ]);

                // Update payment if fully refunded
                if ($data['amount'] == $payment->amount) {
                    $payment->update(['status' => 'refunded']);
                }

                return $refund;
            });

            if ($cacheKey) {
                Redis::setex($cacheKey, 86400, json_encode($refund->toArray()));
            }

            return $refund;

        } catch (ValidationException $e) {
            throw new HttpResponseException(response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422));

        } catch (\Throwable $e) {
            throw new HttpResponseException(response()->json([
                'message' => 'Refund failed.',
                'error' => $e->getMessage(),
                'exception' => class_basename($e),
            ], 500));
        }
    }
}
