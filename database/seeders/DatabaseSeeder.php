<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Pendukung\RincianSeeder;
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
        $this->call(RincianSeeder::class);

        User::factory()->create([
            'name' => 'Muhammad Dhalan',
            'username' => 'dhalan',
            'email' => 'dhalan@gmail.com',
            'password' => bcrypt('qweasdzxc'),
        ]);
    }
}
