<?php

namespace App\DTOs;

class CreateRefundDTO
{
    public function __construct(
        public readonly int $paymentId,
        public readonly int $amount
    ) {}
}
