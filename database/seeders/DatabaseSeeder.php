<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create 5 business clients
        Client::factory(5)->business()->create();

        // Create 5 residential clients
        Client::factory(5)->residential()->create();
    }
}
