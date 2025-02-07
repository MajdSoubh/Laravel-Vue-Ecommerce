<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use App\Events\CartEvent;
use App\Models\Cart;
use App\Repositories\ProductRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\DB;

class StripeWebhookController extends Controller
{
    protected $productRepository;

    /**
     * Constructor to inject ProductRepository dependency.
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Handles incoming Stripe webhook events.
     * Verifies the event signature and processes the event type.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Http\Response
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try
        {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        }
        catch (SignatureVerificationException $e)
        {
            Log::error(__('stripe.signature_verification_failed'));
            return response(__('stripe.invalid_signature'), 400);
        }

        // Handle the event
        switch ($event->type)
        {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSucceeded($paymentIntent);
                break;

            default:
                Log::info(__('stripe.unhandled_event', ['type' => $event->type]));
        }

        return response(__('stripe.webhook_received'), 200);
    }

    /**
     * Processes a successful payment event.
     * Updates payment and order status, decreases product quantities, and clears the user's cart.
     *
     * @param object $paymentIntent The payment intent object from Stripe.
     */
    private function handlePaymentSucceeded($paymentIntent)
    {
        try
        {
            DB::beginTransaction();

            // Retrieve the session ID from the payment intent metadata
            $sessionID = $paymentIntent->metadata['session_id'] ?? null;

            if (!$sessionID)
            {
                throw new \Exception(__('stripe.session_id_not_found'));
            }

            // Find the payment record
            $payment = Payment::where('session_id', $sessionID)
                ->whereIn('status', [PaymentStatus::Pending, PaymentStatus::Paid])
                ->first();

            if (!$payment)
            {
                throw new NotFoundHttpException(__('stripe.payment_not_found'));
            }

            // Update payment and order status if payment is pending
            if ($payment->status === PaymentStatus::Pending)
            {
                $this->changePaymentAndOrderStatusToPaid($payment);

                // Decrease product quantities
                foreach ($payment->order->items as $item)
                {
                    $this->productRepository->decreaseQuantity($item->product_id, $item->quantity);
                }
            }

            // Empty user's cart.
            $userId = $payment->createdBy->id;
            Cart::where('user_id', $userId)->delete();
            CartEvent::dispatch($userId, 'clear');

            DB::commit();

            Log::info(__('stripe.payment_succeeded', ['id' => $paymentIntent->id]));
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            Log::error(__('stripe.payment_processing_error', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Updates the payment and associated order status to "Paid".
     *
     * @param Payment $payment The payment to update.
     */
    private function changePaymentAndOrderStatusToPaid(Payment $payment)
    {
        // Update payment status to Paid
        $payment->status = PaymentStatus::Paid;
        $payment->save();

        // Update associated order status to Paid
        $payment->order->status = OrderStatus::Paid;
        $payment->order->save();
    }
}
