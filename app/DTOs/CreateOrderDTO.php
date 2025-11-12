<?php

namespace App\DTOs;

class CreateOrderDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly array $items // each item: ['product_id' => int, 'quantity' => int]
    ) {}
}
