<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RefundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payment_id' => 1,
            'amount' => 9900,
            'status' => 'completed',
        ];
    }
}
