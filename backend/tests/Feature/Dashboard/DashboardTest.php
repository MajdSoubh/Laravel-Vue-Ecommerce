<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

test('The admin could fetch most request products', function ()
{

    $admin = User::factory()->create(['type' => 'admin']);
    $product_1 = Product::factory()->create(['quantity' => 10]);
    $product_2 = Product::factory()->create(['quantity' => 15]);
    $order = Order::factory()->create(['status' => OrderStatus::Unpaid->value]);
    $order->items()->createMany([['product_id' => $product_1->id, 'quantity' => 7, 'unit_price' => 100], ['product_id' => $product_2->id, 'quantity' => 4, 'unit_price' => 100]]);

    $response = $this->actingAs($admin)->get("/api/admin/dashboard");

    $response->assertStatus(200);
    $response->assertJson([
        'mostRequestedProducts' => ['titles' => [$product_1->title, $product_2->title]],
    ]);
});
test('The admin could fetch active customers', function ()
{

    $admin = User::factory()->create(['type' => 'admin']);
    User::factory()->count(3)->create(['type' => 'client', 'active' => true]);

    $response = $this->actingAs($admin)->get("/api/admin/dashboard");

    $response->assertStatus(200);
    $response->assertJson(
        [
            'activeCustomer' => 3
        ],
    );
});
test('The admin could fetch active published products', function ()
{

    $admin = User::factory()->create(['type' => 'admin']);
    Product::factory()->count(4)->create(['published' => true]);
    Product::factory()->create(['published' => false]);


    $response = $this->actingAs($admin)->get("/api/admin/dashboard");

    $response->assertStatus(200);
    $response->assertJson(
        [
            'activeProducts' => 4
        ],
    );
});
test('The admin could fetch orders Count', function ()
{

    $admin = User::factory()->create(['type' => 'admin']);

    Order::factory()->count(4)->create(['status' => OrderStatus::Paid->value]);


    $response = $this->actingAs($admin)->get("/api/admin/dashboard");

    $response->assertStatus(200);
    $response->assertJson(
        [
            'ordersCount' => 4
        ],
    );
});
test('The admin could fetch total income', function ()
{

    $admin = User::factory()->create(['type' => 'admin']);

    $orders = Order::factory()->count(4)->create(['status' => OrderStatus::Paid->value]);


    $response = $this->actingAs($admin)->get("/api/admin/dashboard");

    $response->assertStatus(200);
    $response->assertJson(
        [
            'totalIncome' => $orders->sum('total_price')
        ],
    );
});
test('The admin could fetch latest active customers', function ()
{

    $admin = User::factory()->create(['type' => 'admin']);
    $clients = User::factory()->count(5)->create(['active' => true, 'type' => 'client']);


    $response = $this->actingAs($admin)->get("/api/admin/dashboard");

    $response->assertStatus(200);
    $response->assertJson(
        [
            'latestCustomer' => $clients->sortByDesc('created_at')->take(3)->toArray()
        ],
    );
});
test('The admin could fetch latest orders', function ()
{

    $admin = User::factory()->create(['type' => 'admin']);

    $orders = Order::factory()->count(5)->create(['status' => OrderStatus::Paid->value]);


    $response = $this->actingAs($admin)->get("/api/admin/dashboard");

    $response->assertStatus(200);
    $response->assertJson(
        [
            'latestOrders' => $orders->sortByDesc('created_at')->select(['id', 'status', 'total_price'])->take(5)->toArray()
        ],
    );
});
