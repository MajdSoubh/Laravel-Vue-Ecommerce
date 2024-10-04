<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

test('The client is allowed to check out their cart items.', function ()
{

    $client = User::factory()->create(['type' => 'client']);
    $product_1 = Product::factory()->create(['quantity' => 10]);
    $product_2 = Product::factory()->create(['quantity' => 5]);
    $this->seed(CountrySeeder::class);
    $data = [
        "email" => "m@m",
        "name" => "majd",
        "address_1" => "s",
        "address_2" => "",
        "state" => null,
        "city" => "syria",
        "country" => "uae",
        "zip_code" => "12",
        "phone" => "1234567890"
    ];
    $client->details()->create(['address_1' => 'tartous', 'city' => 'tartous', 'zipcode' => '1234', 'country_code' => 'uae']);
    // Initialize request data
    $data = [
        "items" => [
            [
                'product_id' => $product_1->id,
                'quantity' => 5,
            ],
            [
                'product_id' => $product_2->id,
                'quantity' => 3,
            ]
        ],
        "success_url" => "http://example.com",
        "cancel_url" => "http://example.com"
    ];
    $response = $this->actingAs($client)->post("/api/checkout", $data);

    $response->assertStatus(200);
    $this->assertMatchesRegularExpression('/^https:\/\/checkout.stripe.com\//', $response->getContent());
});
test('The client is allowed to check out their order.', function ()
{

    $client = User::factory()->create(['type' => 'client']);
    $this->seed(CountrySeeder::class);
    $data = [
        "email" => "m@m",
        "name" => "majd",
        "address_1" => "s",
        "address_2" => "",
        "state" => null,
        "city" => "syria",
        "country" => "uae",
        "zip_code" => "12",
        "phone" => "1234567890"
    ];
    $client->details()->create(['address_1' => 'tartous', 'city' => 'tartous', 'zipcode' => '1234', 'country_code' => 'uae']);

    $product_1 = Product::factory()->create(['quantity' => 10]);
    $order = Order::factory()->create(['created_by' => $client->id, 'updated_by' => $client->id, 'status' => OrderStatus::Unpaid->value]);
    $order->items()->create(['product_id' => $product_1->id, 'quantity' => 2, 'unit_price' => 100]);
    $order->payment()->create(['created_by' => $client->id, 'updated_by' => $client->id, 'status' => OrderStatus::Pending->value, 'type' => 'cc', 'amount' => 200]);

    // Initialize request data
    $data = [
        "success_url" => "http://example.com",
        "cancel_url" => "http://example.com"
    ];
    $response = $this->actingAs($client)->post("/api/checkout/order/$order->id", $data);

    $response->assertStatus(200);
    $this->assertMatchesRegularExpression('/^https:\/\/checkout.stripe.com\//', $response->getContent());
});
