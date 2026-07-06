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
            ['email' => 'anasbinsabiet@gmail.com'],
            [
                'name' => 'Demo Reviewer',
                'password' => Hash::make('Anas@1995'),
            ]
        );
    }
}
