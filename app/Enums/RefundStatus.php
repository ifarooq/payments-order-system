<?php

namespace App\Enums;

enum RefundStatus: string
{
    case Requested = 'requested';
    case Completed = 'completed';
    case Failed    = 'failed';
}
