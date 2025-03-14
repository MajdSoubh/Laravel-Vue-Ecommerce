<?php

namespace App\Services;

use App\Contracts\{
    OrderRepositoryInterface,
    PaymentGatewayInterface,
    PaymentRepositoryInterface,
    ProductRepositoryInterface
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\{OrderStatus, PaymentStatus};

class CheckoutService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
        protected PaymentRepositoryInterface $paymentRepository,
        protected ProductRepositoryInterface $productRepository,
        protected PaymentGatewayInterface $paymentGateway
    )
    {
    }

    /**
     * Processes the checkout for a user's cart.
     *
     * @param array $items Array of cart items, each containing 'product_id' and 'quantity'.
     * @param string $successURL The URL to redirect to after successful payment.
     * @param string $cancelURL The URL to redirect to if payment is canceled.
     * @param int $userId The ID of the user initiating the checkout.
     * @return string The payment gateway session URL where the user should be redirected.
     * @throws \Exception If an error occurs during the checkout process.
     */
    public function processCheckout(array $items, string $successURL, string $cancelURL, int $userId): string
    {
        $ids = array_column($items, 'product_id');
        $products = $this->productRepository->getProductsByIds($ids);
        $cartItems = array_column($items, null, 'product_id');

        $totalPrice = 0;
        $lineItems = [];
        $orderItems = [];

        DB::beginTransaction();

        try
        {
            foreach ($products as $product)
            {
                $quantity = $cartItems[$product->id]['quantity'];
                $totalPrice += $product->price * $quantity;

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product->title,
                        ],
                        'unit_amount_decimal' => $product->price * 100,
                    ],
                    'quantity' => $quantity,
                ];

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                ];
            }

            $session = $this->paymentGateway->initiateCheckout($lineItems, $successURL, $cancelURL);

            // Create Order
            $order = $this->orderRepository->createOrder([
                'total_price' => $totalPrice,
                'status' => OrderStatus::Unpaid,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            // Bind Items to the Order
            $order->items()->createMany($orderItems);

            // Create Payment
            $this->paymentRepository->create([
                'order_id' => $order->id,
                'amount' => $totalPrice,
                'status' => PaymentStatus::Pending,
                'type' => 'cc',
                'created_by' => $userId,
                'updated_by' => $userId,
                'session_id' => $session->id,
            ]);

            DB::commit();
            return $session->url;
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }
    }
}
