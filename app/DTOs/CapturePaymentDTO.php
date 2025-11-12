<?php

namespace App\DTOs;

class CapturePaymentDTO
{
    public function __construct(
        public readonly int $paymentId
    ) {}
}
