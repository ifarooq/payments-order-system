<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{        use HasFactory;

     protected $fillable = [
        'user_id',
        'status',
        'total',
        'currency',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted()
{
    static::saving(function ($order) {
        if (!empty($order->status)) {
            $order->status = strtoupper($order->status);
        }
    });
}

public function payments()
{
    return $this->hasMany(Payment::class);
}

}
