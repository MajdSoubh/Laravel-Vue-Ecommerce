<?php

namespace App\Services;

use App\Repositories\UserCartRepository;

class CartService
{

    public function __construct(private UserCartRepository $cartRepository)
    {
    }

    /**
     * Set user's cart items.
     */
    public function setCartItems(int $userId, array $items): array
    {
        $result = [];

        foreach ($items as $item)
        {
            $result[] = $this->cartRepository->updateOrAddItem($userId, $item['product_id'], $item['quantity']);
        }

        return $result;
    }

    public function updateCartItem(int $userId,  int $productId, int $quantity): array
    {
        $result = $this->cartRepository->updateOrAddItem($userId, $productId, $quantity);

        return $result;
    }
}
