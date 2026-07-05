<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@quantitop.test'],
            [
                'name' => 'Demo Reviewer',
                'password' => Hash::make('Password123'),
            ]
        );
    }
}
