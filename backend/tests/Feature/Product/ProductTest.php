<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;


test('The admin is allowed to create a new Product', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $title = fake()->title();
    $productData = [
        'title' => $title,
        'price' => fake()->numberBetween(0, 2000),
        'quantity' => fake()->numberBetween(0, 100),
        'description' => fake()->paragraph('7'),
        'published' => fake()->randomElement([true, false]),
        'images' => [UploadedFile::fake()->image('test-image.jpg')],
        'categories' => [Category::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id])->id],
    ];

    $response = $this->actingAs($admin)->post("/api/admin/product", $productData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('products', [
        'title' => $title
    ]);
});
test('The client isn\'t allowed to create a new Product', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $title = fake()->title();
    $productData = [
        'title' => $title,
        'price' => fake()->numberBetween(0, 2000),
        'quantity' => fake()->numberBetween(0, 100),
        'description' => fake()->paragraph('7'),
        'published' => fake()->randomElement([true, false]),
        'images' => [UploadedFile::fake()->image('test-image.jpg')],
        'categories' => [Category::factory()->create(['created_by' => $client->id, 'updated_by' => $client->id])->id],
    ];

    $response = $this->actingAs($client)->post("/api/admin/product", $productData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('products', [
        'title' => $title
    ]);
});
test('The admin is allowed to modify an existing Product', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $product = Product::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
    $title = fake()->title();
    $productData = [
        'title' => $title,
        'price' => fake()->numberBetween(0, 2000),
        'quantity' => fake()->numberBetween(0, 100),
        'description' => fake()->paragraph('7'),
        'published' => fake()->randomElement([true, false]),
        'images' => [UploadedFile::fake()->image('test-image.jpg')],
        'categories' => [Category::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id])->id],
    ];

    $response = $this->actingAs($admin)->put("/api/admin/product/$product->id", $productData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('products', [
        'title' => $title
    ]);
});
test('The client isn\'t allowed to modify an existing Product', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $product = Product::factory()->create(['created_by' => $client->id, 'updated_by' => $client->id]);
    $title = fake()->title();
    $productData = [
        'title' => $title,
        'price' => fake()->numberBetween(0, 2000),
        'quantity' => fake()->numberBetween(0, 100),
        'description' => fake()->paragraph('7'),
        'published' => fake()->randomElement([true, false]),
        'images' => [UploadedFile::fake()->image('test-image.jpg')],
        'categories' => [Category::factory()->create(['created_by' => $client->id, 'updated_by' => $client->id])->id],
    ];

    $response = $this->actingAs($client)->put("/api/admin/product/$product->id", $productData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('products', [
        'title' => $title
    ]);
});

test('The admin is allowed to delete an existing Product', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $product = Product::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

    $response = $this->actingAs($admin)->delete("/api/admin/product/$product->id");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
test('The client isn\'t allowed to delete an existing Product', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $product = Product::factory()->create(['created_by' => $client->id, 'updated_by' => $client->id]);

    $response = $this->actingAs($client)->delete("/api/admin/product/$product->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
    ]);
});
