<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate([
            'name'     => 'Admin',
            'nim'      => 'admin',
            'role'     => 'admin',
            'password' => Hash::make('admin123'),
        ]);
    }
}