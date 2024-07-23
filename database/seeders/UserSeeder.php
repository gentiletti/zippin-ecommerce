<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Juan Perez',
            'email' => 'juan.perez@example.com',
            'password' => bcrypt('password')
        ]);

        User::create([
            'name' => 'María Gómez',
            'email' => 'maria.gomez@example.com',
            'password' => bcrypt('password')
        ]);
    }
}
