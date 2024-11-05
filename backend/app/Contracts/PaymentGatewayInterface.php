<?php

namespace App\Contracts;

/**
 * PaymentGatewayInterface defines the contract for payment services.
 */
interface PaymentGatewayInterface
{
    /**
     * Initiates the checkout process for the payment service.
     *
     * @param array $items Array of items to be purchased.
     * @param string $successURL URL to redirect to after successful payment.
     * @param string $cancelURL URL to redirect to if the payment is canceled.
     * @return \Stripe\Checkout\Session Session for checkout.
     */
    public function initiateCheckout(array $items, string $successURL, string $cancelURL);

    /**
     * Finalizes the payment and processes the order after checkout.
     *
     * @param string $sessionID The ID of the session to finalize the order.
     * @return mixed Payment details or customer information.
     */
    public function finalizeOrder(string $sessionID);
}
