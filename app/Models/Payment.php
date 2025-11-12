<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
class Payment extends Model
{
     use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'currency',
        'status',
        'reference',
        'authorized_at',
        'captured_at',
        'voided_at',
    ];

    protected $casts = [
        'authorized_at' => 'datetime',
        'captured_at'   => 'datetime',
        'voided_at'     => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
