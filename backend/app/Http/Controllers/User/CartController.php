<?php

namespace App\Http\Controllers\User;

use App\Events\CartEvent;
use App\Events\Notification;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Cart\StoreRequest;
use App\Http\Requests\User\Cart\UpdateRequest;
use App\Http\Resources\Cart\CartResource;
use App\Http\Resources\Product\CartProductResource;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Constructor to inject the CartService dependency.
     *
     * @param \App\Services\CartService $cartService The service responsible for cart operations.
     */
    public function __construct(protected CartService $cartService)
    {
    }

    /**
     * Retrieve the authenticated user's cart with associated products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentUserCart()
    {
        $cart = auth()->user()->cart()->with('product')->get();
        return CartResource::collection($cart);
    }

    /**
     * Replace the entire contents of the authenticated user's cart.
     *
     * @param \App\Http\Requests\User\Cart\StoreRequest $request Validated request containing cart items.
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCart(StoreRequest $request)
    {
        $user = Auth::user();
        $cartItems = $this->cartService->setFullCart($user->id, $request->validated('items'));
        $cartItems = CartResource::collection($cartItems);

        CartEvent::dispatch(auth()->user()->id, 'overwrite', $cartItems->toArray($request));
        Notification::dispatch(__('cart.uploaded'), 'success');

        return response()->json(['success' => true, 'data' => $cartItems]);
    }

    /**
     * Check if the request cart items quantity is available and update user's cart if authenticated.
     *
     * This method handles updating the cart for both authenticated users and guests.
     * If the user is authenticated, it delegates the logic to `updateUserCart`. Otherwise,
     * it delegates the logic to `updateGuestCart`
     *
     * @param \App\Http\Requests\User\Cart\UpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCart(UpdateRequest $request)
    {
        $data = $request->validated();

        return auth()->check() ? $this->updateUserCart($data) : $this->updateGuestCart($data);
    }

    /**
     * Update the cart for an authenticated user.
     *
     * This method updates the cart item in the database for the authenticated user, dispatches
     * a cart event, and sends a notification about the update.
     *
     * @param array $data An array containing:
     *        - product_id (int): The ID of the product to be updated in the cart.
     *        - quantity (int): The new quantity of the product in the cart.
     * @return \Illuminate\Http\JsonResponse
     */
    private function updateUserCart(array $data)
    {
        $cartItem = $this->cartService->updateCartItem(auth()->user()->id, $data['product_id'], $data['quantity']);

        $cartItem = new CartResource($cartItem);

        CartEvent::dispatch(auth()->user()->id, 'merge', $cartItem->toArray(resolve(Request::class)));

        Notification::dispatch(__('cart.updated', ['product' => $cartItem['product']['title']]), 'success');

        return response()->json(['success' => true, 'data' => $cartItem]);
    }

    /**
     * Update the cart for a guest user.
     *
     * This method updates the cart for guest users by preparing the cart item data on the client side.
     * It does not persist the cart data in the database but sends a notification about the update.
     *
     * @param array $data An array containing:
     *        - product_id (int): The ID of the product to be updated in the cart.
     *        - quantity (int): The new quantity of the product in the cart.
     * @return \Illuminate\Http\JsonResponse
     */
    private function updateGuestCart(array $data)
    {
        $product = Product::findOrFail($data['product_id']);
        $cartItem = array_merge(
            ['product_id' => $data['product_id'], 'quantity' => $data['quantity']],
            ['product' => new CartProductResource($product)]
        );

        Notification::dispatch(__('cart.updated', ['product' => $cartItem['product']['title']]), 'success');

        return response()->json(['success' => true, 'data' => $cartItem]);
    }

    /**
     * Remove a specific product from the authenticated user's cart.
     *
     * @param int $productId The ID of the product to be removed.
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(int $productId)
    {
        $cartItem = $this->cartService->removeItem(auth()->user()->id, $productId);

        Notification::dispatch(__('cart.removed', ['product' => $cartItem->product->title]), 'success');

        return response()->json([
            'success' => true,
            'message' => __('cart.removed', ['product' => $cartItem->product->title]),
        ]);
    }
}
