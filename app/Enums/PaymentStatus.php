<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case AUTHORIZED = 'AUTHORIZED';
    case CAPTURED = 'CAPTURED';
    case VOIDED = 'VOIDED';
    case REFUNDED = 'REFUNDED';
}
