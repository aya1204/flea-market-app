<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => '山田 太郎',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'image' => 'user1_icon.png',
        ]);
        User::create([
            'name' => '佐藤 花子',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'image' => 'user2_icon.png',
        ]);
        User::create([
            'name' => '鈴木 一郎',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'image' => 'user3_icon.png',
        ]);
    }
}
