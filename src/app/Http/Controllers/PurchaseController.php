<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Paymentmethod;

class PurchaseController extends Controller
{
    /**
     * 商品購入ページ表示
     */
    public function index(Item $item)
    {

        $paymentmethods = Paymentmethod::all();

        $selected_paymentmethod = request()->query('paymentmethod_id');

        // 支払い方法の名前を取得
        $method_name = optional($paymentmethods->firstWhere('id', $selected_paymentmethod))->name;

        $user = auth()->user();

        return view('purchase.purchase', compact('item', 'paymentmethods', 'user', 'selected_paymentmethod', 'method_name'));
    }
}
