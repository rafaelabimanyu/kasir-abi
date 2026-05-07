<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@kasirabi.com',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Kasir 01',
            'email'    => 'kasir@kasirabi.com',
            'password' => 'password',
            'role'     => 'kasir',
        ]);
    }
}
