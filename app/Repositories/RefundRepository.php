<?php

namespace App\Repositories;

use App\Models\Refund;

class RefundRepository
{
    public function create(array $data): Refund
    {
        return Refund::create($data);
    }

    public function findById(int $id): ?Refund
    {
        return Refund::find($id);
    }

    public function findByPaymentId(int $paymentId)
    {
        return Refund::where('payment_id', $paymentId)->get();
    }
}
