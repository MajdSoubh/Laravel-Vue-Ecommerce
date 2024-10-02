<?php

namespace App\Http\Controllers\User;

use App\Enums\HttpStatusCode;

use App\Events\Cart\ItemUpdated;
use App\Events\Notify;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Cart\StoreRequest;
use App\Http\Requests\User\Cart\UpdateRequest;
use App\Models\Product;
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

                $cartItem =  $user->cart()->create(['product_id' => $item['product_id'], 'quantity' => $item['quantity']]);
            }
        }

        Notify::dispatch("The shopping cart has been stored successfully.", 'success');

        return response()->json(['data' => $cartItem->toArray()]);
    }

    public function updateCart(UpdateRequest $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Retrieve the product.
        $product = Product::select(['id', 'title', 'price'])->whereId($productId)->first();

        // If user is authenticated update his cart
        /** @var \App\Models\User $user */
        $user = auth('sanctum')->user();

        if ($user)
        {
            $cartItem = $user->cart()->where('product_id', $productId)->first();

            // If the Product already exists on the user's cart update it's quantity.
            if ($cartItem)
            {
                $cartItem->quantity = $quantity;
                $cartItem->update();
            }
            else
            {
                $cartItem =  $user->cart()->create($request->validated());
            }

            Notify::dispatch("{$product->title} quantity has been updated successfully", 'success', $product->toArray());
        }



        // If user not authenticated and quantity available send notification.
        Notify::dispatch("{$product->title} quantity has been updated successfully", 'success', $product->toArray());
    }

    public function delete(string $productId)
    {

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $item = $user->cart()->where('product_id', $productId)->first();

        // Retrieve the product.
        $product = Product::select(['id', 'title'])->whereId($productId)->first();

        $item->delete();

        // send notification.
        Notify::dispatch("{$product->title} has been removed successfully", 'success');
    }
}
