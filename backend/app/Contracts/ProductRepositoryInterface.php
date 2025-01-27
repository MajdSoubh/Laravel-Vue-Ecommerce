<?php

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    /**
     * Get products by their IDs.
     */
    public function getProductsByIds(array $ids): Collection;

    /**
     * Decrease the quantity of a product.
     */
    public function decreaseQuantity(int $productId, int $quantity): void;
}
