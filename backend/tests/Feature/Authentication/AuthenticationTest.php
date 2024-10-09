<?php


use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
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
test('The admin can trigger a password forget', function ()
{
    $admin = User::factory()->create(['type' => 'admin', 'email' => 'majdsoubh53@gmail.com']);
    Notification::fake();

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
    Notification::assertSentTo($admin, ResetPassword::class, function ($notification)
    {
        $resetToken = $notification->token;

        $this->assertNotEmpty($resetToken);

        return true;
    });
});
test('The admin can reset their password using reset token', function ()
{
    Notification::fake();

    $admin = User::factory()->create(['type' => 'admin', 'email' => 'majdsoubh53@gmail.com']);

    // Trigger password reset request
    $this->post('/api/admin/forget-password', [
        'email' => 'majdsoubh53@gmail.com'
    ]);

    // Capture the reset token
    Notification::assertSentTo($admin, ResetPassword::class, function ($notification) use ($admin)
    {
        $resetToken = $notification->token;

        // Use the token to reset the password
        $response = $this->post('/api/admin/reset-password', [
            'token' => $resetToken,
            'email' => $admin->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Assert password was reset
        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newpassword', $admin->fresh()->password));

        return true;
    });
});

test('The client can trigger a password forget', function ()
{
    $client = User::factory()->create(['type' => 'client', 'email' => 'majdsoubh53@gmail.com']);
    Notification::fake();

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

    // Assert that the password reset mail has been sent.
    Notification::assertSentTo($client, ResetPassword::class, function ($notification)
    {
        $resetToken = $notification->token;

        $this->assertNotEmpty($resetToken);

        return true;
    });
});
test('The client can reset their password using reset token', function ()
{
    Notification::fake();

    $admin = User::factory()->create(['type' => 'client', 'email' => 'majdsoubh53@gmail.com']);

    // Trigger password reset request
    $this->post('/api/forget-password', [
        'email' => 'majdsoubh53@gmail.com'
    ]);

    // Capture the reset token
    Notification::assertSentTo($admin, ResetPassword::class, function ($notification) use ($admin)
    {
        $resetToken = $notification->token;

        // Use the token to reset the password
        $response = $this->post('/api/reset-password', [
            'token' => $resetToken,
            'email' => $admin->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        // Assert password was reset
        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newpassword', $admin->fresh()->password));

        return true;
    });
});
