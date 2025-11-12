<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderRepository
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function findWithItems(int $id): ?Order
    {
        return Order::with('items.product')->find($id);
    }

    public function findByUser(int $userId)
    {
        return Order::where('user_id', $userId)
            ->with('items.product')
            ->get();
    }

   public function createWithItems(array $orderData, array $items): Order
    {
        return DB::transaction(function () use ($orderData, $items) {
            /** @var Order $order */
            $order = Order::create(Arr::only($orderData, ['user_id', 'status', 'total', 'currency']));

            // ensure order items relationship exists on model
            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['line_total'],
                ]);
            }

            return $order;
        });
    }


}
