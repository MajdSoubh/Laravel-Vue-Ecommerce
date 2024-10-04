<?php


use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;

test('Admin can login', function ()
{
    $admin = User::factory()->create(['type' => 'admin', 'email' => 'admin@admin', 'password' => bcrypt('mmmmmmmm')]);
    $credentials = ['email' => 'admin@admin', 'password' => 'mmmmmmmm', 'password_confirmation' => 'mmmmmmmm'];

    $response = $this->post("/api/admin/login", $credentials);

    $response->assertStatus(200);
    $this->assertAuthenticatedAs($admin, 'sanctum');
});

test('Client can login', function ()
{
    $client = User::factory()->create(['type' => 'client', 'email' => 'client@client', 'password' => bcrypt('mmmmmmmm')]);
    $credentials = ['email' => 'client@client', 'password' => 'mmmmmmmm', 'password_confirmation' => 'mmmmmmmm'];

    $response = $this->post("/api/login", $credentials);

    $response->assertStatus(200);
    $this->assertAuthenticatedAs($client, 'sanctum');
});

test('Autenticated admin can logout', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);

    // Simulate the user being logged in
    Sanctum::actingAs($admin);

    // Assert the user is authenticated
    $this->assertTrue(auth()->check());
    $this->assertNotNull($admin->currentAccessToken());

    // Perform a GET request to the logout route
    $response = $this->get('/api/admin/logout');

    // Assertion
    $admin->fresh();
    $this->assertNotNull($admin->currentAccessToken());
    $response->assertStatus(200);
});
test('Autenticated client can logout', function ()
{
    $client = User::factory()->create(['type' => 'client']);

    // Simulate the user being logged in
    Sanctum::actingAs($client);

    // Assert the user is authenticated
    $this->assertTrue(auth()->check());
    $this->assertNotNull($client->currentAccessToken());

    // Perform a GET request to the logout route
    $response = $this->get('/api/logout');

    // Assertion
    $client->fresh();
    $this->assertNotNull($client->currentAccessToken());
    $response->assertStatus(200);
});
test('Unautenticated admin cannot logout', function ()
{
    $admin = User::factory()->create(['type' => 'admin']);

    // Assert the user is Unauthenticated
    $this->assertFalse(auth()->check());
    $this->assertNull($admin->currentAccessToken());

    // Perform a GET request to the logout route
    $response = $this->get('/api/admin/logout');

    // Assertion
    $response->assertStatus(401);
});
test('Unautenticated client cannot logout', function ()
{
    $client = User::factory()->create(['type' => 'client']);

    // Assert the user is Unauthenticated
    $this->assertFalse(auth()->check());
    $this->assertNull($client->currentAccessToken());

    // Perform a GET request to the logout route
    $response = $this->get('/api/logout');

    // Assertion
    $response->assertStatus(401);
});
test('Admin can trigger a password forget', function ()
{
    User::factory()->create(['type' => 'admin', 'email' => 'majdsoubh53@gmail.com']);
    Mail::fake();

    // Call the forgetPassword method
    $response = $this->post('/api/admin/forget-password', [
        'email' => 'majdsoubh53@gmail.com'
    ]);



    // Assert successful response
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => __(Password::RESET_LINK_SENT)
        ]);

    // Assert that the password reset mail has been sent.
    // Mail::assertSent(ResetPassword::class, function ($mail)
    // {
    //     return $mail->hasTo('majdsoubh53@gmail.com');
    // });
});
test('Client can trigger a password forget', function ()
{
    User::factory()->create(['type' => 'client', 'email' => 'majdsoubh53@gmail.com']);

    // Call the forgetPassword method
    $response = $this->post('/api/forget-password', [
        'email' => 'majdsoubh53@gmail.com'
    ]);

    // Assert successful response
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => __(Password::RESET_LINK_SENT)
        ]);
});
