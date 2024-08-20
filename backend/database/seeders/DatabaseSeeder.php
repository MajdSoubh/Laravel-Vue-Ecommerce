<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin',
            'password' => 'mmmmmmmm',

            'type' => 'admin',

        ]);
        User::factory()->create([
            'name' => 'Reqular User',
            'email' => 'user@user',
            'password' => 'mmmmmmmm',

            'type' => 'client',

        ]);

        $this->call([

            CountrySeeder::class
        ]);
    }
}
