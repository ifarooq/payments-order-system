<?php

namespace App\DTOs;

class AuthorizePaymentDTO
{
    public function __construct(
        public readonly int $orderId
    ) {}
}
