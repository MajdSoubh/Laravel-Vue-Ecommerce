<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // Check if the database is already seeded
        if ($this->isAlreadySeeded())
        {
            $this->command->info('Database already seeded!');
            return;
        }

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

    private function isAlreadySeeded()
    {

        // Check if any table has existing records, like the 'users' table.
        return DB::table('users')->count() > 0 && DB::table('countries')->count() > 0;
    }
}
