<?php

namespace App\Http\Controllers\User;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Cart\StoreRequest;
use App\Http\Requests\User\Cart\UpdateRequest;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function getCurrentUserCart()
    {
        /** @var \App\Models\User $user */
        $user = Auth::User();
        $cart = $user->cart()->get();

        return response()->json($cart, HttpStatusCode::OK->value);
    }

    public function setCart(StoreRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        foreach ($request->input('items') as $item)
        {
            $cartItem = $user->cart()->where('product_id', $item['product_id'])->first();
            if ($cartItem)
            {
                $cartItem->quantity = $item['quantity'];
                $cartItem->update();
            }
            else
            {
                $user->cart()->create(['product_id' => $item['product_id'], 'quantity' => $item['quantity']]);
            }
        }
        return response()->json(['message' => 'Cart has been updated successfully'], HttpStatusCode::OK->value);
    }

    public function updateCart(UpdateRequest $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');


        // If user is authenticated update his cart
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if ($user)
        {
            $cart = $user->cart()->where('product_id', $productId)->first();
            // return dd($user->cart()->get());
            // If the Product already exists on the user's cart update it's quantity.
            if ($cart)
            {
                $cart->quantity = $quantity;
                $cart->update();
            }
            else
            {
                $cart =  $user->cart()->create($request->validated());
            }
            return response()->json(['message' => 'The cart has been updated', 'cart' => $cart], HttpStatusCode::OK->value);
        }

        // If user not authenticated and quantity available return success message
        return response()->json(['message' => 'The requested quantity is available in the Stock'], HttpStatusCode::OK->value);
    }

    public function delete(string $productId)
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $cart = $user->cart()->where('product_id', $productId)->first();

        $cart->delete();
        return response()->json(['message' => 'The Item has been deleted from the Cart'], HttpStatusCode::OK->value);
    }
}
