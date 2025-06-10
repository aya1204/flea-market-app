<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;

class SellController extends Controller
{
    public function index()
    {
        return view('sell.sell'); // resources/views/sell/sell.blade.php を表示
    }

    public function create(ExhibitionRequest $request)
    {
        return redirect('/')->with('success', '商品を出品しました。');
    }
}
