<?php

namespace App\Http\Controllers\User;

use App\Enums\HttpStatusCode;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Checkout\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
        /** @var App\Models\User $user */
        $user = auth()->user();
        if (!isset($user->details->country) && !isset($user->details->address_1))
        {
            return response()->json(['message' => "Please Complete your profile shippment details"], HttpStatusCode::BAD_REQUEST->value);
        }
        $items = $request->input('items');
        $successUrl = $request->input('success_url');
        $cancelUrl = $request->input('cancel_url');

        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
        $ids = Arr::pluck($items, 'product_id');
        $products = Product::whereIn('id', $ids)->get();
        $cartItems = Arr::keyBy($items, 'product_id');

        $totalPrice = 0;
        $lineItems = [];
        $orderItems = [];

        try
        {
            // Start A transaction
            DB::beginTransaction();

            foreach ($products as $product)
            {
                $quantity = $cartItems[$product->id]['quantity'];

                // Update the Total price
                $totalPrice += $product->price * $quantity;

                // Push to LineItems
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product->title,
                            // 'images' => $product->images ? $product->images->pluck('path') : []
                        ],
                        'unit_amount_decimal' => $product->price * 100,
                    ],
                    'quantity' => $quantity,
                ];

                // Push to OrderItems
                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price
                ];

                // Update the Product quantity
                if ($product->quantity !== null)
                {
                    $product->quantity -= $quantity;
                    $product->save();
                }
            }

            // Create Strip Payment Session
            $session = \Stripe\Checkout\Session::create([
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_creation' => 'always',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl,
            ]);


            // Create Order
            $order = Order::create([
                'total_price' => $totalPrice,
                'status' => OrderStatus::Unpaid,
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
            ]);

            // Create Order Items
            foreach ($orderItems as $orderItem)
            {
                $orderItem['order_id'] = $order->id;
                OrderItem::create($orderItem);
            }

            // Create Payment
            Payment::create([
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'status' => PaymentStatus::Pending,
                'type' => 'cc',
                'created_by' => $request->user()->id,
                'updated_by' => $request->user()->id,
                'session_id' => $session->id
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }
        DB::commit();
        Cart::where(['user_id' => $request->user()->id,])->delete();

        // Return payment page url
        return $session->url;
    }


    public function success(Request $request)
    {
        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
        try
        {
            $session_id = $request->get('session_id');
            $session = \Stripe\Checkout\Session::retrieve($session_id);
            if (!$session)
            {
                return response()->json(['message' => 'invalid session id'], HttpStatusCode::BAD_REQUEST->value);
            }

            $payment = Payment::query()
                ->where(['session_id' => $session_id])
                ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
                ->first();
            if (!$payment)
            {
                throw new NotFoundHttpException();
            }
            if ($payment->status === PaymentStatus::Pending->value)
            {
                $this->updateOrderAndSession($payment);
            }
            $customer = \Stripe\Customer::retrieve($session->customer);

            return response()->json(['message' => "Order has been completed", 'customer' => $customer], HttpStatusCode::OK->value);
        }
        catch (\Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], HttpStatusCode::BAD_REQUEST->value);
        }
    }
    public function checkoutOrder(Order $order, Request $request)
    {

        /** @var App\Models\User $user */
        $user = auth()->user();
        if (!isset($user->details->country) && !isset($user->details->address_1))
        {
            return response()->json(['message' => "Please Complete your profile shippment details"], HttpStatusCode::BAD_REQUEST->value);
        }
        $successUrl = $request->input('success_url');
        $cancelUrl = $request->input('cancel_url');

        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
        $lineItems = [];
        foreach ($order->items as $item)
        {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->title,
                        //                        'images' => [$product->image]
                    ],
                    'unit_amount' => $item->unit_price * 100,
                ],
                'quantity' => $item->quantity,
            ];
        }

        $session = \Stripe\Checkout\Session::create([
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelUrl
        ]);

        $order->payment->session_id = $session->id;
        $order->payment->save();


        return $session->url;
    }
    private function updateOrderAndSession(Payment $payment)
    {
        DB::beginTransaction();
        try
        {
            $payment->status = PaymentStatus::Paid->value;
            $payment->update();

            $order = $payment->order;

            $order->status = OrderStatus::Paid->value;
            $order->update();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }

        DB::commit();
    }
}
