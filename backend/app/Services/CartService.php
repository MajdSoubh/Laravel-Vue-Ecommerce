<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Update or create multiple cart items for a user.
     *
     * @param int $userId User ID.
     * @param array $items Array of items with `product_id` and `quantity`.
     * @return Collection Collection of user's cart items.
     */
    public function setFullCart(int $userId, array $items): Collection
    {
        $cartData = collect($items)->map(function ($item) use ($userId)
        {
            return [
                'user_id' => $userId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ];
        })->toArray();

        Cart::upsert(
            $cartData,
            ['user_id', 'product_id'],
            ['quantity']
        );

        $result = Cart::with('product')->where('user_id', $userId)->get();

        return $result;
    }

    /**
     * Update or create a single cart item for a user.
     *
     * @param int $userId User ID.
     * @param int $productId Product ID.
     * @param int $quantity Product quantity.
     * @return Cart Updated/created Cart model.
     */
    public function updateCartItem(int $userId,  int $productId, int $quantity): Cart
    {
        $result = Cart::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $productId,
            ],
            [
                'quantity' => $quantity,
            ]
        );

        $result->load('product');

        return $result;
    }

    /**
     * Remove item from the cart.
     *
     * @param int $userId User ID.
     * @param int $productId Product ID.
     * @return Cart removed Cart model.
     */
    public function removeItem(int $userId, int $productId): Cart
    {
        $result = Cart::where(['user_id' => $userId, 'product_id' => $productId])
            ->with(['product' => fn ($q) => $q->select('id', 'title')])
            ->firstOrFail();


        $result->delete();

        return $result;
    }
}
