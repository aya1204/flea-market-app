<?php

namespace App\Http\Controllers;

class SellController extends Controller
{
    // 出品画面表示
    public function index()
    {
        return view('sell.sell'); // resources/views/sell/sell.blade.php を表示
    }
}
