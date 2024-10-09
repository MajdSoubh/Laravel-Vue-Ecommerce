<?php

use App\Models\User;

test('Client can update his informations', function ()
{
    $client = User::factory()->create(['email' => 'm@m', 'type' => 'client']);
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

    $response = $this->actingAs($client)->post("/api/customer/details", $data);

    $response->assertStatus(200);
});
