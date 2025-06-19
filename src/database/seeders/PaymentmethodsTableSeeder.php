<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentmethodsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('paymentmethods')->insert([
            ['id' => 1, 'name' => 'カード払い'],
            ['id' => 2, 'name' => 'コンビニ払い'],
        ]);
    }
}
