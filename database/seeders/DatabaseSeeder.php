<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Restoran',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Kasir Restoran',
            'email' => 'kasir@example.com',
            'role' => 'kasir',
        ]);

        User::factory()->create([
            'name' => 'Pelayan Restoran',
            'email' => 'pelayan@example.com',
            'role' => 'pelayan',
        ]);

        User::factory()->create([
            'name' => 'Pelanggan Restoran',
            'email' => 'pelanggan@example.com',
            'role' => 'customer',
        ]);

        User::factory()->create([
            'name' => 'Koki Restoran',
            'email' => 'koki@example.com',
            'role' => 'koki',
        ]);

        User::factory()->create([
            'name' => 'Gudang Restoran',
            'email' => 'gudang@example.com',
            'role' => 'gudang',
        ]);
    }
}
