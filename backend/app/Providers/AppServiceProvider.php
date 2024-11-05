<?php

namespace App\Providers;

use App\Services\Payments\StripePayment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use App\Contracts\PaymentGatewayInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Binding the interface to the default payment implementation (Stripe)
        $this->app->bind(PaymentGatewayInterface::class, function ($app)
        {
            return new StripePayment(); // Or use PayPalPaymentService based on your needs
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::$wrap = null;
    }
}
