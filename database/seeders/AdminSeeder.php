<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'dicowww',
            'email' => 'dikocesrt@gmail.com',
            'password' => Hash::make('diko1234'),
        ]);

        User::create([
            'name' => 'vitriw',
            'email' => 'kvitriseptia@gmail.com',
            'password' => Hash::make('021223'),
        ]);
    }
}
