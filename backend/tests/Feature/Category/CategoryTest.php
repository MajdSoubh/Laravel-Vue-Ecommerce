<?php

use App\Models\Category;
use App\Models\User;

test('The admin is allowed to retrieve all Categories', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);

    $response = $this->actingAs($admin)->get("/api/admin/category");

    $response->assertStatus(200);
});
test('The client is allowed to retrieve all Categories', function ()
{
    $client = User::factory()->create(['type' => 'client']);

    $response = $this->actingAs($client)->get("/api/category");

    $response->assertStatus(200);
});
test('The admin is allowed to create a new Category', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $categoryData = ['name' => 'Laptop', 'active' => true];

    $response = $this->actingAs($admin)->post("/api/admin/category", $categoryData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('categories', [
        'name' => 'Laptop'
    ]);
});
test('The client isn\'t allowed to create a new Category', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $categoryData = ['name' => 'Laptop', 'active' => true];

    $response = $this->actingAs($client)->post("/api/admin/category", $categoryData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('categories', [
        'name' => 'Laptop'
    ]);
});
test('The admin is allowed to modify an existing Category', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $category = Category::factory()->create(['name' => 'Electronics', 'active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id]);

    $categoryData = ['name' => 'Laptop'];

    $response = $this->actingAs($admin)->put("/api/admin/category/$category->id", $categoryData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('categories', [
        'name' => 'Laptop'
    ]);
});

test('The client isn\'t allowed to modify an existing Category', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $category = Category::factory()->create(['name' => 'Electronics', 'active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id]);
    $client = User::factory()->create(['type' => 'client']);

    $categoryData = ['name' => 'Laptop'];

    $response = $this->actingAs($client)->put("/api/admin/category/$category->id", $categoryData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('categories', [
        'name' => 'Laptop'
    ]);
});
test('The admin is allowed to delete an existing Category', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $category = Category::factory()->create(['name' => 'Electronics', 'active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id]);

    $response = $this->actingAs($admin)->delete("/api/admin/category/$category->id");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('categories', [
        'name' => 'Electronics'
    ]);
});
test('The client isn\'t allowed to delete an existing Category', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $category = Category::factory()->create(['name' => 'Electronics', 'active' => true, 'created_by' => $admin->id, 'updated_by' => $admin->id]);
    $client = User::factory()->create(['type' => 'client']);

    $response = $this->actingAs($client)->delete("/api/admin/category/$category->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('categories', [
        'name' => 'Electronics'
    ]);
});
