<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function create(array $data): Payment
{
    $payment = Payment::create($data);
    return $payment->load('order'); // <— Add this
}

    public function find(int $id): ?Payment
    {
        return Payment::find($id);
    }

    public function findByOrderId(int $orderId): ?Payment
    {
        return Payment::where('order_id', $orderId)->first();
    }

 
    public function findById(int $id): Payment
    {
        return Payment::with('order')->findOrFail($id);
    }

 
}
