<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserStatus;
use App\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name"=> "Admin",
            "email"=> "admin@email.com",
            'username'=> 'admin',
            'password'=> Hash::make('12345'),
            'type'=> UserType::SuperAdmnin,
            'status' => UserStatus::Active
        ]);
    }
}
