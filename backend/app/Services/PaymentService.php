<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;

/**
 * PaymentService is a class that delegates payment processing
 * to the appropriate payment provider (e.g., Stripe, PayPal).
 */
class PaymentService
{
    /**
     * @var PaymentGatewayInterface
     */
    protected $paymentProvider;

    /**
     * Constructor to inject the payment provider.
     *
     * @param PaymentGatewayInterface $paymentProvider
     */
    public function __construct(PaymentGatewayInterface $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

    /**
     * Initiates checkout via the selected payment provider.
     *
     * @param array $items
     * @param string $successURL
     * @param string $cancelURL
     * @return \Stripe\Checkout\Session
     */
    public function initiateCheckout(array $items, string $successURL, string $cancelURL)
    {
        return $this->paymentProvider->initiateCheckout($items, $successURL, $cancelURL);
    }

    /**
     * Finalizes the payment process using the payment provider.
     *
     * @param string $sessionID
     * @return mixed
     */
    public function finalizeOrder(string $sessionID)
    {
        return $this->paymentProvider->finalizeOrder($sessionID);
    }
}
