
<?php

use App\Models\User;

test('The admin is allowed to fetch all account', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $users = User::factory()->count(5)->create();
    $users->unshift($admin);
    $response = $this->actingAs($admin)->get('/api/admin/user');

    $response->assertStatus(200);
    $response->assertJson(
        [
            'data' => $users->select(['id', 'name', 'email', 'type', 'phone'])->toArray()
        ],
    );
});
test('The client is not allowed to fetch all account', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $users = User::factory()->count(5)->create();
    $users->unshift($client);
    $response = $this->actingAs($client)->get('/api/admin/user');

    $response->assertStatus(401);
});
test('The visitor is not allowed to fetch all account', function ()
{
    $users = User::factory()->count(5)->create();

    $response = $this->get('/api/admin/user');

    $response->assertStatus(401);
});
test('The admin is allowed to create a new admin account', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $newAdminData = ['name' => 'test_user', 'email' => 'admin@admin', "password" => "mmmmmmmm", "password_confirmation" => "mmmmmmmm", "type" => "admin", "active" => true];

    $response = $this->actingAs($admin)->post('/api/admin/user', $newAdminData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['name' => 'Test_user', 'email' => 'admin@admin']);
});
test('The admin is allowed to create a new client account', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $clientData = ['name' => 'Test_user', 'email' => 'client@client', "password" => "mmmmmmmm", "password_confirmation" => "mmmmmmmm", "type" => "client", "active" => true];

    $response = $this->actingAs($admin)->post('/api/admin/user', $clientData);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', ['name' => 'Test_user', 'email' => 'client@client']);
});
test('The client isn\'t allowed to create a new admin account', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $newAdminData = ['name' => 'test_user', 'email' => 'admin@admin', "password" => "mmmmmmmm", "password_confirmation" => "mmmmmmmm", "type" => "admin", "active" => true];

    $response = $this->actingAs($client)->post('/api/admin/user', $newAdminData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('users', ['name' => 'Test_user']);
});
test('The admin is allowed to modify other admins account data', function ()
{
    $admin_1 = User::factory()->create(['type' => 'admin']);
    $admin_2 = User::factory()->create(['type' => 'admin']);
    $adminData = ['name' => 'test_user'];

    $response = $this->actingAs($admin_1)->put("/api/admin/user/$admin_2->id", $adminData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('users', ['name' => 'Test_user']);
});
test('The admin is allowed to modify other clients account data', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $client = User::factory()->create(['type' => 'client']);
    $clientData = ['name' => 'test_user'];

    $response = $this->actingAs($admin)->put("/api/admin/user/$client->id", $clientData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('users', ['name' => 'Test_user']);
});
test('The client isn\'t allowed to modify other admins account data', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $admin = User::factory()->create(['type' => 'admin']);
    $clientData = ['name' => 'test_user'];

    $response = $this->actingAs($client)->put("/api/admin/user/$admin->id", $clientData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('users', ['name' => 'Test_user']);
});
test('The client isn\'t allowed to modify other clients account data', function ()
{
    $client_1 = User::factory()->create(['type' => 'client']);
    $client_2 = User::factory()->create(['type' => 'client']);
    $clientData = ['name' => 'test_user', 'email' => 'test_mail@test_mail', "password" => "mmmmmmmm", "password_confirmation" => "mmmmmmmm"];

    $response = $this->actingAs($client_1)->put("/api/admin/user/$client_2->id", $clientData);

    $response->assertStatus(401);
    $this->assertDatabaseMissing('users', ['name' => 'Test_user', 'email' => 'test_mail@test_mail']);
});
test('The admin is allowed to delete another admins account', function ()
{
    $admin_1 = User::factory()->create(['type' => 'admin']);
    $admin_2 = User::factory()->create(['type' => 'admin']);

    $response = $this->actingAs($admin_1)->delete("/api/admin/user/$admin_2->id");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('users', ['name' => $admin_2->name, 'email' => $admin_2->email]);
});

test('The admin is allowed to delete another clients account', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);
    $client = User::factory()->create(['type' => 'client']);

    $response = $this->actingAs($admin)->delete("/api/admin/user/$client->id");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('users', ['name' => $client->name, 'email' => $client->email]);
});
test('The client isn\'t allowed to delete another clients account', function ()
{
    $client_1 = User::factory()->create(['type' => 'client']);
    $client_2 = User::factory()->create(['type' => 'client']);

    $response = $this->actingAs($client_1)->delete("/api/admin/user/$client_2->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('users', ['name' => $client_2->name, 'email' => $client_2->email]);
});
test('The client isn\'t allowed to delete another admins account', function ()
{
    $client = User::factory()->create(['type' => 'client']);
    $admin = User::factory()->create(['type' => 'admin']);

    $response = $this->actingAs($client)->delete("/api/admin/user/$admin->id");

    $response->assertStatus(401);
    $this->assertDatabaseHas('users', ['name' => $admin->name, 'email' => $admin->email]);
});
