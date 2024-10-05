<?php

use App\Models\User;
use App\Models\Order;

test('The client could fetch their orders', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $orders = Order::factory()->count(4)->create(['created_by' => $client->id, 'updated_by' => $client->id]);

    $response = $this->actingAs($client)->get("/api/order");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => $orders->select(['id', 'total_price', 'status'])->toArray(),
    ]);
});
test('The client could fetch their specific order', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $orders = Order::factory()->count(4)->create(['created_by' => $client->id, 'updated_by' => $client->id]);
    $orderID = $orders->first()->id;

    $response = $this->actingAs($client)->get("/api/order/$orderID");

    $response->assertStatus(200);
    $response->assertJson(
        $orders->select(['id', 'total_price', 'status'])->first(),
    );
});
