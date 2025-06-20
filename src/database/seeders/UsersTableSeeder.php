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
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
            'building' => ' 千駄ヶ谷マンション101',
        ]);
        User::create([
            'name' => '佐藤 花子',
            'email' => 'test2@example.com',
            'password' => Hash::make('password'),
            'image' => 'user2_icon.png',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
            'building' => ' 千駄ヶ谷マンション102',
        ]);
        User::create([
            'name' => '鈴木 一郎',
            'email' => 'test3@example.com',
            'password' => Hash::make('password'),
            'image' => 'user3_icon.png',
            'postal_code' => '123-4567',
            'address' => '東京都渋谷区千駄ヶ谷1-2-3',
            'building' => ' 千駄ヶ谷マンション103',
        ]);
    }
}
