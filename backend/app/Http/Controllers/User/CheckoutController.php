<?php

namespace App\Http\Controllers\User;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Checkout\CheckoutRequest;
use App\Contracts\ProductRepositoryInterface;
use App\Events\CartEvent;
use App\Models\Cart;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Services\CheckoutService;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected ProductRepositoryInterface $productRepository
    )
    {
    }

    /**
     * Process the checkout for the authenticated user.
     *
     * This method validates the request, checks if the user's profile is complete,
     * and processes the checkout using the `CheckoutService`.
     *
     * @param \App\Http\Requests\User\Checkout\CheckoutRequest $request Validated request containing checkout details.
     * @return mixed Redirect URL for payment processing.
     */
    public function checkout(CheckoutRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        // Ensure the user has completed their profile shipment details
        if (!isset($user->details->country) || !isset($user->details->address_1))
        {
            return response()->json(
                ['message' => __('checkout.profile_incomplete')],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Process the checkout and return the redirect URL
        $redirectUrl = $this->checkoutService->processCheckout(
            $validated['items'],
            $validated['success_url'],
            $validated['cancel_url'],
            $user->id
        );

        return $redirectUrl;
    }

    /**
     * Handle the success callback after a successful payment.
     *
     * This method updates the payment and order status to "Paid," decreases product quantities,
     * and clears the user's cart.
     *
     * @param \Illuminate\Http\Request $request Request containing the session ID.
     * @return \Illuminate\Http\JsonResponse JSON response indicating success or failure.
     */
    public function success(Request $request)
    {
        try
        {
            $sessionID = $request->get('session_id');
            $payment = Payment::where('session_id', $sessionID)
                ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
                ->first();

            if (!$payment)
            {
                throw new NotFoundHttpException(__('checkout.payment_not_found'));
            }

            // Update payment and order status if pending
            if ($payment->status === PaymentStatus::Pending)
            {
                $this->changePaymentAndOrderStatusToPaid($payment);

                // Decrease product quantities after successful payment
                foreach ($payment->order->items as $item)
                {
                    $this->productRepository->decreaseQuantity($item->product_id, $item->quantity);
                }
            }

            // Clear the user's cart
            $userId = $payment->createdBy->id;
            Cart::where('user_id', $userId)->delete();
            CartEvent::dispatch($userId, 'clear');

            return response()->json(
                ['message' => __('checkout.order_completed')],
                Response::HTTP_OK
            );
        }
        catch (\Exception $e)
        {
            return response()->json(
                ['message' => __('checkout.error_occurred', ['error' => $e->getMessage()])],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * Update the payment and order status to "Paid."
     *
     * This method ensures atomicity by wrapping the updates in a database transaction.
     *
     * @param \App\Models\Payment $payment The payment to be updated.
     * @throws \Exception If an error occurs during the transaction.
     */
    private function changePaymentAndOrderStatusToPaid(Payment $payment)
    {
        DB::beginTransaction();
        try
        {
            $payment->status = PaymentStatus::Paid;
            $payment->save();

            $order = $payment->order;
            $order->status = OrderStatus::Paid;
            $order->save();

            DB::commit();
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Log::critical(__METHOD__ . ' method failed. ' . $e->getMessage());
            throw $e;
        }
    }
}
