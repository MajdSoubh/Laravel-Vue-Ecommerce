<?php

namespace App\Http\Controllers\User;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Checkout\CheckoutRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckoutController extends Controller
{

    public function __construct(public PaymentService $paymentService)
    {
    }

    public function checkout(CheckoutRequest $request)
    {
        /** @var App\Models\User $user */
        $user = auth()->user();
        if (!isset($user->details->country) && !isset($user->details->address_1))
        {
            return response()->json(['message' => "Please Complete your profile shippment details"], Response::HTTP_BAD_REQUEST);
        }
        $items = $request->input('items');
        $successURL = $request->input('success_url');
        $cancelURL = $request->input('cancel_url');

        // \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
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

            // Create payment session
            $session = $this->paymentService->initiateCheckout($lineItems, $successURL, $cancelURL);


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
        try
        {
            $sessionID = $request->get('session_id');

            // Get payment customer
            $customer = $this->paymentService->finalizeOrder($sessionID);

            $payment = Payment::query()
                ->where(['session_id' => $sessionID])
                ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
                ->first();
            if (!$payment)
            {
                throw new NotFoundHttpException();
            }
            if ($payment->status === PaymentStatus::Pending->value)
            {
                $this->changePaymentAndOrderStatusToPaid($payment);
            }
            $customer = \Stripe\Customer::retrieve($customer);

            return response()->json(['message' => "Order has been completed", 'customer' => $customer], Response::HTTP_OK);
        }
        catch (\Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    public function checkoutOrder(Order $order, Request $request, PaymentService $paymentService)
    {

        /** @var App\Models\User $user */
        $user = auth()->user();
        if (!isset($user->details->country) && !isset($user->details->address_1))
        {
            return response()->json(['message' => "Please Complete your profile shippment details"], Response::HTTP_BAD_REQUEST);
        }
        $successURL = $request->input('success_url');
        $cancelURL = $request->input('cancel_url');

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

        // Create payment session
        $session = $this->paymentService->initiateCheckout($lineItems, $successURL, $cancelURL);


        $order->payment->session_id = $session->id;
        $order->payment->save();


        return $session->url;
    }
    private function changePaymentAndOrderStatusToPaid(Payment $payment)
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
