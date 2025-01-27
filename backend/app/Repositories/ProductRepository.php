<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryInterface
{
    public function getProductsByIds(array $ids): Collection
    {
        return Product::whereIn('id', $ids)->get();
    }

    public function decreaseQuantity(int $productId, int $quantity): void
    {
        $product = Product::find($productId);
        if ($product && $product->quantity !== null)
        {
            $product->decrement('quantity', $quantity);
        }
    }
}
