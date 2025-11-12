<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
class PaymentController extends Controller
{
     public function __construct(private PaymentService $paymentService) {}

    // public function authorizePayment(Request $request): JsonResponse
    // {
    //     $idempotencyKey = $request->header('Idempotency-Key');
    //     $data = $request->validate([
    //         'order_id' => 'required|integer|exists:orders,id',
    //     ]);

    //     $payment = $this->paymentService->authorizePayment($data, $idempotencyKey);
    //     dd(get_class($payment), $payment);
    //     //return response()->json($payment, 201);
    //     return response()->json(is_array($payment) ? $payment : $payment->toArray(), 201);
    // }


//     public function authorizePayment(Request $request): JsonResponse
// {
//      dd([
//     'body' => $request->all(),
//     'headers' => [
//         'Idempotency-Key' => $request->header('Idempotency-Key'),
//         'Content-Type' => $request->header('Content-Type'),
//     ],
// ]);
    
//     $idempotencyKey = $request->header('Idempotency-Key');
//      dd($request->all($idempotencyKey));


//     if (empty($idempotencyKey)) {
//         return response()->json(['error' => 'Missing Idempotency-Key header'], 400);
//     }

//     if (!$request->isJson()) {
//         return response()->json(['error' => 'Request must be JSON'], 400);
//     }

//     $data = $request->validate([
//         'order_id' => 'required|integer|exists:orders,id',
//     ]);

//     return response()->json(['ok' => true, 'data' => $data]);
// }
public function authorizePayment(Request $request): JsonResponse
{
    $idempotencyKey = $request->header('Idempotency-Key');
 
    //dd($idempotencyKey);
   try {
    $data = $request->validate([
        'order_id' => ['required', 'integer', 'exists:orders,id'],
    ]);
} catch (\Illuminate\Validation\ValidationException $e) {
    return response()->json([
        'message' => 'Validation failed',
        'errors' => $e->errors(),
    ], 422);
}

//dd('hi');
    $payment = $this->paymentService->authorizePayment($data, $idempotencyKey);
   // dd($payment);
    return response()->json($payment, 201);
}

   public function capturePayment(int $id): JsonResponse
{
    $payment = $this->paymentService->capturePayment($id);
    return response()->json($payment);
}

    public function voidPayment(int $id): JsonResponse
    {
        $payment = $this->paymentService->voidPayment($id);
        return response()->json($payment);
    }
}
