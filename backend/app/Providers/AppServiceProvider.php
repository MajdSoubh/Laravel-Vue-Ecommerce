<?php

namespace App\Providers;

use App\Services\Payments\StripePayment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use App\Contracts\PaymentGatewayInterface;
use App\Contracts\{OrderRepositoryInterface, PaymentRepositoryInterface, ProductRepositoryInterface};
use App\Repositories\{OrderRepository, PaymentRepository, ProductRepository};

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
            return new StripePayment();
        });
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::$wrap = null;
    }
}
