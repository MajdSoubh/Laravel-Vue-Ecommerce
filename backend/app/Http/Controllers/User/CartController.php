<?php

namespace App\Http\Controllers\User;

use App\Events\CartEvent;
use App\Events\Notification;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Cart\StoreRequest;
use App\Http\Requests\User\Cart\UpdateRequest;
use App\Http\Resources\Cart\CartResource;
use App\Http\Resources\Product\ProductResource;
use App\Models\Cart;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function __construct(protected CartService $cartService)
    {
    }

    /**
     * Get authenticated user's cart with products
     */
    public function getCurrentUserCart()
    {
        $cart =  auth()->user()->cart()->with('product')->get();

        return CartResource::collection($cart);
    }

    /**
     * Replace entire cart contents
     */
    public function setCart(StoreRequest $request)
    {
        $user = Auth::user();

        $result = $this->cartService->setFullCart($user->id,  $request->validated('items'));

        $cartIds = $result->pluck('id');
        $cartItems = Cart::whereIn('id', $cartIds)->with('product')->get();

        CartEvent::dispatch(auth()->user()->id, 'overwrite', $cartItems->toArray());
        Notification::dispatch(__('cart.uploaded'), 'success');

        return CartResource::collection($cartItems);
    }

    /**
     * Update specific cart item quantity
     */
    public function updateCart(UpdateRequest $request)
    {
        $validated = $request->validated();

        // Update user's cart if authenticated otherwise the cart is updated in the client-side.
        if (auth()->check())
        {
            $this->cartService->updateCartItem(auth()->user()->id, $validated['product_id'], $validated['quantity']);
        }

        $product = new ProductResource(Product::findOrFail($validated['product_id']));

        $cartItem = array_merge(
            ['product_id' => $validated['product_id'], 'quantity' => $validated['quantity']],
            ['product' => $product]
        );

        CartEvent::dispatch(auth()->user()->id, 'merge', $cartItem);
        Notification::dispatch(__('cart.updated', ['product' => $cartItem['product']['title']]), 'success');

        return response()->json(['success' => true, 'data' => $cartItem]);
    }

    /**
     * Remove a product from the user's cart.
     */
    public function delete(int $productId)
    {
        $cartItem = $this->cartService->removeItem(auth()->user()->id, $productId);

        Notification::dispatch(__('cart.removed', ['product' => $cartItem->product->title]), 'success');

        return response()->json(['success' => true, 'message' => __('cart.removed', ['product' => $cartItem->product->title])]);
    }
}
