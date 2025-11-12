<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RefundController;
use App\Http\Controllers\Api\ReportController;

Route::prefix('orders')->group(function () {
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::get('/', [OrderController::class, 'index']);
});

Route::prefix('payments')->group(function () {
    Route::post('/', [PaymentController::class, 'authorizePayment']);
    Route::post('{id}/capture', [PaymentController::class, 'capturePayment']);
    Route::post('{id}/void', [PaymentController::class, 'voidPayment']);
});

Route::post('refunds', [RefundController::class, 'processRefund']);
Route::get('reports/daily-settlement', [ReportController::class, 'dailySettlement']);
