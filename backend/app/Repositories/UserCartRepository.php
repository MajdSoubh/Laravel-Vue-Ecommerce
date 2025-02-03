<?php

namespace App\Repositories;

use App\Contracts\UserCartRepositoryInterface;
use App\Models\Cart;

class UserCartRepository implements UserCartRepositoryInterface
{

    public function addItem(int $userId, int $productId, int $quantity): array
    {
        $result = Cart::create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'user_id' => $userId,
        ])->toArray();

        return $result;
    }

    public function updateItemQuantity(int $userId, int $productId, int $quantity): array
    {
        $result = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->update(['quantity' => $quantity])->toArray();

        return $result;
    }

    public function updateOrAddItem(int $userId, int $productId, int $quantity): array
    {
        $result = Cart::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $productId,
            ],
            [
                'quantity' => $quantity,
            ]
        )->toArray();

        return $result;
    }
}
