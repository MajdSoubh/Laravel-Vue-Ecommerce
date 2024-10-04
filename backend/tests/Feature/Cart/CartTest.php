<?php

use App\Events\Notify;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Event;

test('The client is allowed to retrieve his cart', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $product_1 = Product::factory()->create(['quantity' => 10]);
    $product_2 = Product::factory()->create(['quantity' => 5]);
    Cart::factory()->count(2)->sequence(['product_id' => $product_1->id], ['product_id' => $product_2->id])->create([
        'user_id' => $client->id,
        'quantity' => 2,
    ]);

    $response = $this->actingAs($client)->get("/api/cart");

    $response->assertStatus(200);
    $response->assertJson([
        [
            'product_id' => $product_1->id,
            'user_id' => $client->id,
            'quantity' => 2,
        ],
        [
            'product_id' => $product_2->id,
            'user_id' => $client->id,
            'quantity' => 2,
        ]
    ]);
});

test('The client is allowed to update his cart', function ()
{

    $client = User::factory()->create(['type' => 'client']);
    $product = Product::factory()->create(['quantity' => 10]);
    Cart::factory()->create([
        'user_id' => $client->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $newCartData = [
        'product_id' => $product->id,
        'quantity' => 5,
    ];
    Event::fake();

    // Call the updateCart method
    $this->actingAs($client)->put('/api/cart', $newCartData);


    // Assert that the notification was dispatched
    Event::assertDispatched(Notify::class, function ($event) use ($product)
    {
        return $event->message === "{$product->title} quantity has been updated successfully" &&
            $event->type === 'success' &&
            $event->data['id'] == $product->id;
    });

    // Assert that the existing item was updated
    $this->assertDatabaseHas('carts', [
        'user_id'    => $client->id,
        'product_id' =>  $product->id,
        'quantity'   => 5,
    ]);
});

test('The visitor is allowed to update his cart', function ()
{

    $product = Product::factory()->create(['quantity' => 10]);
    $newCartData = [
        'product_id' => $product->id,
        'quantity' => 5,
    ];

    Event::fake();

    // Call the updateCart method
    $this->put('/api/cart', $newCartData);


    // Assert that the notification was dispatched
    Event::assertDispatched(Notify::class, function ($event) use ($product)
    {
        return $event->message === "{$product->title} quantity has been updated successfully" &&
            $event->type === 'success' &&
            $event->data['id'] == $product->id;
    });
});
test('The client is allowed to upload his shop cart', function ()
{

    $client = User::factory()->create(['type' => 'client']);
    $product_1 = Product::factory()->create(['quantity' => 10]);
    $product_2 = Product::factory()->create(['quantity' => 7]);

    $cartData = [
        "items" => [
            [
                'product_id' => $product_1->id,
                'quantity' => 5,
            ],
            [
                'product_id' => $product_2->id,
                'quantity' => 3,
            ]
        ]
    ];
    Event::fake();


    $this->actingAs($client)->post('/api/cart', $cartData);


    // Assert that the notification was dispatched
    Event::assertDispatched(Notify::class, function ($event) use ($cartData)
    {
        return $event->message === "The shopping cart has been stored successfully." &&
            $event->type === 'success';
    });

    // Assert that the cart has been updated
    $this->assertDatabaseHas('carts', [
        'user_id'    => $client->id,
        'product_id' =>  $cartData['items'][0]['product_id'],
        'quantity'   => $cartData['items'][0]['quantity'],
    ]);
    $this->assertDatabaseHas('carts', [
        'user_id'    => $client->id,
        'product_id' =>  $cartData['items'][1]['product_id'],
        'quantity'   => $cartData['items'][1]['quantity'],
    ]);
});
