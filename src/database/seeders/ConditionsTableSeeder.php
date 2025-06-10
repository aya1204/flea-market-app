<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('conditions')->insert([
            ['name' => '良好'],               // ID:1
            ['name' => '目立った傷や汚れなし'], // ID:2
            ['name' => 'やや傷や汚れあり'],    // ID:3
            ['name' => '状態が悪い'],         // ID:4
        ]);
    }
}
