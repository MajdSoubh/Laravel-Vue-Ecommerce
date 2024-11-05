<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * StripePayment class implements PaymentInterface to handle Stripe payments.
 */
class StripePayment implements PaymentGatewayInterface
{
    public function __construct()
    {
        // Set the Stripe API key.
        Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));
    }

    /**
     * {@inheritdoc}
     */
    public function initiateCheckout(array $items, string $successURL, string $cancelURL)
    {
        $session = Session::create([
            'line_items' => $items,
            'mode' => 'payment',
            'customer_creation' => 'always',
            'success_url' => $successURL . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $cancelURL,
        ]);

        return $session;
    }

    /**
     * {@inheritdoc}
     */
    public function finalizeOrder(string $sessionID)
    {
        $session = Session::retrieve($sessionID);

        if (!$session)
        {
            throw new NotFoundHttpException();
        }

        return Customer::retrieve($session->customer);
    }
}
