<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('The admin could fetch products', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $product_1 = Product::factory()->create(['published' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id]);
    $product_2 = Product::factory()->create(['published' => false, 'created_by' => $admin->id, 'updated_by' => $admin->id]);

    $response = $this->actingAs($admin)->get("/api/admin/product");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'id' => $product_1->id,
            'title' => $product_1->title
        ], [
            'id' => $product_2->id,
            'title' => $product_2->title
        ]],
    ]);
});
test('The client could fetch only published products', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $product_1 = Product::factory()->create(['published' => true]);
    $product_2 = Product::factory()->create(['published' => false]);

    $response = $this->actingAs($client)->get("/api/product");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'id' => $product_1->id,
            'title' => $product_1->title
        ]],
    ]);
    $response->assertJsonMissing([
        'data' => [[
            'id' => $product_2->id,
            'title' => $product_2->title
        ]],
    ]);
});
test('The visitor could fetch only published products', function ()
{
    $product_1 = Product::factory()->create(['published' => true]);
    $product_2 = Product::factory()->create(['published' => false]);

    $response = $this->get("/api/product");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'id' => $product_1->id,
            'title' => $product_1->title
        ]],
    ]);
    $response->assertJsonMissing([
        'data' => [[
            'id' => $product_2->id,
            'title' => $product_2->title
        ]],
    ]);
});
test('The client could fetch specific published product', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $product_1 = Product::factory()->create(['published' => true]);
    $product_2 = Product::factory()->create(['published' => false]);

    $response = $this->actingAs($client)->get("/api/product/$product_1->id");

    $response->assertStatus(200);
    $response->assertJson(

        [
            'id' => $product_1->id,
            'title' => $product_1->title
        ],

    );
    $response->assertJsonMissing(
        [
            'id' => $product_2->id,
            'title' => $product_2->title
        ],
    );
});
test('The visitor could fetch specific published product', function ()
{
    $product_1 = Product::factory()->create(['published' => true]);
    $product_2 = Product::factory()->create(['published' => false]);

    $response = $this->get("/api/product/$product_1->id");

    $response->assertStatus(200);
    $response->assertJson(

        [
            'id' => $product_1->id,
            'title' => $product_1->title
        ],

    );
    $response->assertJsonMissing(
        [
            'id' => $product_2->id,
            'title' => $product_2->title
        ],
    );
});
test('The admin could search for specific product', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $product_1 = Product::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
    $product_2 = Product::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);

    $response = $this->actingAs($admin)->get("/api/admin/product?search=$product_1->title");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'id' => $product_1->id,
            'title' => $product_1->title
        ]],
    ]);
    $response->assertJsonMissing([
        'data' => [[
            'id' => $product_2->id,
            'title' => $product_2->title
        ]],
    ]);
});
test('The client could search for specific product', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $product_1 = Product::factory()->create(['published' => true]);
    $product_2 = Product::factory()->create(['published' => true]);

    $response = $this->actingAs($client)->get("/api/product?search=$product_1->title");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'title' => $product_1->title
        ]],
    ]);
    $response->assertJsonMissing([
        'data' => [[
            'title' => $product_2->title
        ]],
    ]);
});
test('The visitor could search for specific product', function ()
{
    $product_1 = Product::factory()->create(['published' => true]);
    $product_2 = Product::factory()->create(['published' => true]);

    $response = $this->get("/api/product?search=$product_1->title");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'title' => $product_1->title
        ]],
    ]);
    $response->assertJsonMissing([
        'data' => [[
            'title' => $product_2->title
        ]],
    ]);
});
test('The client could filter products by category', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $product_1 = Product::factory()->create(['published' => true]);
    $category_1 = Category::factory()->create(['active' => true]);
    $category_1->products()->attach($product_1);
    $product_2 = Product::factory()->create(['published' => true]);
    $category_2 = Category::factory()->create(['active' => true]);
    $category_2->products()->attach($product_2);
    $categoryName = $product_1->categories->first()->name;
    $response = $this->actingAs($client)->get("/api/product?categories[]=$categoryName");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'title' => $product_1->title
        ]],
    ]);
    $response->assertJsonMissing([
        'data' => [[
            'title' => $product_2->title
        ]],
    ]);
});
test('The visitor could filter products by category', function ()
{
    $product_1 = Product::factory()->create(['published' => true]);
    $category_1 = Category::factory()->create(['active' => true]);
    $category_1->products()->attach($product_1);
    $product_2 = Product::factory()->create(['published' => true]);
    $category_2 = Category::factory()->create(['active' => true]);
    $category_2->products()->attach($product_2);
    $categoryName = $product_1->categories->first()->name;
    $response = $this->get("/api/product?categories[]=$categoryName");

    $response->assertStatus(200);
    $response->assertJson([
        'data' => [[
            'title' => $product_1->title
        ]],
    ]);
    $response->assertJsonMissing([
        'data' => [[
            'title' => $product_2->title
        ]],
    ]);
});
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
        'categories' => [Category::factory()->create()->id],
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
        'categories' => [Category::factory()->create()->id],
    ];

    $response = $this->actingAs($client)->post("/api/admin/product", $productData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('products', [
        'title' => $title
    ]);
});
test('The visitor isn\'t allowed to create a new Product', function ()
{
    $title = fake()->title();
    $productData = [
        'title' => $title,
        'price' => fake()->numberBetween(0, 2000),
        'quantity' => fake()->numberBetween(0, 100),
        'description' => fake()->paragraph('7'),
        'published' => fake()->randomElement([true, false]),
        'images' => [UploadedFile::fake()->image('test-image.jpg')],
        'categories' => [Category::factory()->create()->id],
    ];

    $response = $this->post("/api/admin/product", $productData);

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
    $product = Product::factory()->create();
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
test('The visitor isn\'t allowed to modify an existing Product', function ()
{
    $product = Product::factory()->create();
    $title = fake()->title();
    $productData = [
        'title' => $title,
        'price' => fake()->numberBetween(0, 2000),
        'quantity' => fake()->numberBetween(0, 100),
        'description' => fake()->paragraph('7'),
        'published' => fake()->randomElement([true, false]),
        'images' => [UploadedFile::fake()->image('test-image.jpg')],
        'categories' => [Category::factory()->create()->id],
    ];

    $response = $this->put("/api/admin/product/$product->id", $productData);

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
    $product = Product::factory()->create();

    $response = $this->actingAs($client)->delete("/api/admin/product/$product->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
    ]);
});
test('The visitor isn\'t allowed to delete an existing Product', function ()
{
    $product = Product::factory()->create();

    $response = $this->delete("/api/admin/product/$product->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
    ]);
});
