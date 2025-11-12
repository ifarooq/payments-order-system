<?php

namespace App\DTOs;

class VoidPaymentDTO
{
    public function __construct(
        public readonly int $paymentId
    ) {}
}
