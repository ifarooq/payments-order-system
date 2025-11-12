<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
class ProductRepository
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function findManyByIds($ids): Collection
    {
        return Product::whereIn('id', $ids)->get();
    }

      public function getPriceAndCurrency(int $id): ?array
    {
        $p = $this->findById($id);
        if (!$p) return null;

        return [
            'price' => (int) $p->price,
            'currency' => $p->currency ?? 'MYR',
        ];
    }
}
