<?php

namespace App\Services;

use App\DTOs\CreateOrderDTO;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class OrderService
{
    public function __construct(
        private ProductRepository $productRepo,
        private OrderRepository $orderRepo,
    ) {}

         
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Validate that all requested products exist
            $productIds = collect($data['items'])->pluck('product_id');
            $products = $this->productRepo->findManyByIds($productIds);

            if ($products->count() !== count($data['items'])) {
                throw new ModelNotFoundException('One or more products not found.');
            }

            // 2. Create the order
            $order = $this->orderRepo->create([
                'user_id' => $data['user_id'],
                'status' => OrderStatus::PENDING->value,
                'total'  => 0,
            ]);

            $total = 0;

            // 3. Create order items
            foreach ($data['items'] as $item) {
                $product = $products->firstWhere('id', $item['product_id']);
                $unitPrice = $product->price; // stored in minor units (e.g. 9900 for MYR 99.00)
                $lineTotal = $unitPrice * $item['quantity'];

                OrderItem::create([
                    'order_id'    => $order->id,
                    'product_id'  => $product->id,
                    'unit_price'  => $unitPrice,
                    'quantity'    => $item['quantity'],
                    'line_total'  => $lineTotal,
                ]);

                $total += $lineTotal;
            }

            // 4. Update order total
            $order->update(['total' => $total]);

            // 5. Return order with its items and product details
            return $order->load('items.product');
        });
    }



    public function getAllOrders()
    {
        $orders = Order::all(); // directly using the model
        return $orders;
    }
}
