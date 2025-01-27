<?php

namespace App\Http\Controllers\User;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Checkout\CheckoutRequest;
use App\Contracts\ProductRepositoryInterface;
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

    public function __construct(protected CheckoutService $checkoutService, protected ProductRepositoryInterface $productRepository)
    {
    }

    public function checkout(CheckoutRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        if (!isset($user->details->country) && !isset($user->details->address_1))
        {
            return response()->json(['message' => "Please complete your profile shipment details"], Response::HTTP_BAD_REQUEST);
        }

        $redirectUrl = $this->checkoutService->processCheckout(
            $validated['items'],
            $validated['success_url'],
            $validated['cancel_url'],
            $user->id
        );


        return $redirectUrl;
    }

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
                throw new NotFoundHttpException();
            }

            if ($payment->status === PaymentStatus::Pending)
            {
                $this->changePaymentAndOrderStatusToPaid($payment);
                // Decrease product quantities after successful payment
                foreach ($payment->order->items as $item)
                {
                    $this->productRepository->decreaseQuantity($item->product_id, $item->quantity);
                }
            }

            // Empty user Cart.
            Cart::where('user_id', $payment->createdBy->id)->delete();

            return response()->json(['message' => "Order has been completed"], Response::HTTP_OK);
        }
        catch (\Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

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
            Log::critical(__METHOD__ . ' method does not work. ' . $e->getMessage());
            throw $e;
        }
    }
}
