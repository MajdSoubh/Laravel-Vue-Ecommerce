<?php

namespace App\Contracts;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;

interface UserCartRepositoryInterface
{
    /**
     * Add an item to the user's cart.
     *
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @return array
     */
    public function addItem(int $userId, int $productId, int $quantity): array;

    /**
     * Update the quantity of a specific cart item.
     *
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @return array
     */
    public function updateItemQuantity(int $userId, int $productId, int $quantity): array;

    /**
     * Update or add a new cart item.
     *
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @return array
     */
    public function updateOrAddItem(int $userId, int $productId, int $quantity): array;
}
