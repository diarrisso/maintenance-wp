<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::factory()->create([
            'name' => 'Max Mustermann',
            'email' => 'admin@masingatech.com',
            'password' => bcrypt('password'),
        ]);

        // Techniciens
        User::factory()->create([
            'name' => 'Anna Weber',
            'email' => 'anna@masingatech.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'Thomas Schneider',
            'email' => 'thomas@masingatech.com',
            'password' => bcrypt('password'),
        ]);
    }
}
