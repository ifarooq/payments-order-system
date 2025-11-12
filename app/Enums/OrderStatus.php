<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case FULFILLED = 'FULFILLED';
    case CANCELLED = 'CANCELLED';
}
