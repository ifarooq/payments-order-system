<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\OrderRepository;
use App\Enums\PaymentStatus;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
class PaymentService
{
    public function __construct(
        private PaymentRepository $paymentRepo,
        private OrderRepository $orderRepo
    ) {}

    /**
     * Authorize a payment with idempotency
     */
public function authorizePayment(array $data, string $idempotencyKey)
{
   Log::info('AuthorizePayment called', [
        'idempotencyKey' => $idempotencyKey,
        'data' => $data,
    ]);

    try {
        if (empty($idempotencyKey)) {
            throw ValidationException::withMessages([
                'Idempotency-Key' => ['Idempotency-Key header is required.'],
            ]);
        }

        $cacheKey = "idempotency:{$idempotencyKey}";

        if ($existing = Redis::get($cacheKey)) {
            Log::info('Returning cached payment', ['key' => $cacheKey]);
            return json_decode($existing, true);
        }

        // --- DB Transaction (only for DB logic) ---
        $payment = null;
        DB::beginTransaction();

        try {
            $order = $this->orderRepo->findWithItems($data['order_id']);
            if (!$order) {
                throw new ModelNotFoundException('Order not found.');
            }

            if (strtolower($order->status) !== 'pending') {
                throw ValidationException::withMessages([
                    'order' => ['Only pending orders can be paid.'],
                ]);
            }

            $payment = $this->paymentRepo->create([
                'order_id' => $order->id,
                'amount'   => $order->total,
                'currency' => 'MYR',
                'status'   => 'authorized',
                'reference'=> (string) Str::uuid(),
            ]);

            DB::commit();
            Log::info('Payment committed to DB', ['payment_id' => $payment->id]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DB transaction failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        // --- Cache after commit ---
        try {
            Redis::setex($cacheKey, 86400, json_encode($payment->toArray()));
        } catch (\Throwable $e) {
            Log::error('Redis failed (non-blocking)', ['error' => $e->getMessage()]);
        }

        // --- Return the persisted payment record ---
        $payment = $this->paymentRepo->findById($payment->id);
        Log::info('Returning payment response', ['payment' => $payment->toArray()]);
        return $payment;

    } catch (ValidationException $e) {
        Log::warning('Validation failed', ['errors' => $e->errors()]);
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422));

    } catch (ModelNotFoundException $e) {
        Log::warning('Order not found', ['error' => $e->getMessage()]);
        throw new HttpResponseException(response()->json([
            'message' => 'Order not found or invalid ID.',
        ], 404));

    } catch (\Throwable $e) {
        Log::error('Payment authorization failed', ['error' => $e->getMessage()]);
        throw new HttpResponseException(response()->json([
            'message' => 'Payment authorization failed.',
            'error' => $e->getMessage(),
            'exception' => class_basename($e),
        ], 500));
    }
}



    /**
     * Capture a payment
     */
   public function capturePayment(int $paymentId)
{
    try {
        return DB::transaction(function () use ($paymentId) {
            $payment = $this->paymentRepo->findById($paymentId);

            // Idempotent: return same if already captured
            if (strtolower($payment->status) === 'captured') {
                return $payment->fresh();
            }

            if (strtolower($payment->status) !== 'authorized') {
                throw ValidationException::withMessages([
                    'payment' => ['Only AUTHORIZED payments can be captured.'],
                ]);
            }

            // Ensure timestamp and status both set
            $payment->status = 'captured';
            $payment->captured_at = now();
            $payment->save();

            if ($payment->order) {
                $payment->order->status = 'confirmed';
                $payment->order->save();
            }

            // Force database reload to ensure fresh timestamps
            return $payment->fresh();
        });
    } catch (ValidationException $e) {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422));
    } catch (\Throwable $e) {
        throw new HttpResponseException(response()->json([
            'message' => 'Payment capture failed.',
            'error' => $e->getMessage(),
            'exception' => class_basename($e),
        ], 500));
    }
}

    /**
     * Void a payment
     */
 public function voidPayment(int $paymentId)
{
    try {
        $payment = $this->paymentRepo->findById($paymentId);

        if (strtolower($payment->status) !== 'authorized') {
            throw ValidationException::withMessages([
                'payment' => ['Only authorized payments can be voided.'],
            ]);
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status'    => 'voided',
                'voided_at' => now(),
            ]);

            $payment->order->update([
                'status' => 'cancelled',
            ]);
        });

        return $payment->fresh();

    } catch (ValidationException $e) {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors'  => $e->errors(),
        ], 422));

    } catch (ModelNotFoundException $e) {
        throw new HttpResponseException(response()->json([
            'message' => 'Payment not found or invalid ID.',
        ], 404));

    } catch (\Throwable $e) {
        Log::error('Payment void failed', [
            'payment_id' => $paymentId,
            'error' => $e->getMessage(),
        ]);

        throw new HttpResponseException(response()->json([
            'message' => 'Payment void failed.',
            'error' => $e->getMessage(),
            'exception' => class_basename($e),
        ], 500));
    }
}
}
