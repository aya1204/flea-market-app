<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Paymentmethod;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PurchaseController extends Controller
{
    // 商品購入ページ表示
    public function index(Item $item)
    {
        $paymentmethods = Paymentmethod::all();
        $item = Item::find($item->id);

        $selectedPaymentMethod = request()->query('paymentmethod_id');

        // 支払い方法の名前を取得
        $methodName = optional($paymentmethods->firstWhere('id', $selectedPaymentMethod))->name;

        return view('purchase.purchase', compact('item', 'paymentmethods', 'user', 'selectedPaymentMethod', 'methodName'));
    }

    // 商品購入処理
    public function create(PurchaseRequest $request, Item $item)
    {

        // すでに売れているかチェック
        if ($item->is_sold) {
            return redirect()->route('items.show', $item)->with('error', 'この商品はすでに購入されています。');
        }

        // Stripe APIキー
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        // 支払い方法の取得
        $paymentMethodId = $request->input('paymentmethod_id');
        $paymentMethod = Paymentmethod::find($paymentMethodId);

        // 支払いタイプ判定
        $paymentType = $paymentMethod->name === 'コンビニ支払い' ? 'conveniencestore' : 'card';

        // Checkoutセッション作成
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => [$paymentType],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->title,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('mypage'), // 成功後プロフィール画面へ
            'cancel_url' => route('purchase.index', ['item' => $item->id]),
            ]);

            return redirect($session->url);
    }

    // 住所変更画面を表示
    public function address(Item $item)
    {
        $user = Auth::user();
        return view('purchase.delivery_address_edit', compact('user', 'item'));
    }

    // 住所変更保存
    public function update(AddressRequest $request, Item $item)
    {

        /** @var \App\Models\Item $item */
        $item->update([
            'is_sold' => true,
            'purchase_user_id' => Auth::id(),
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update($request->validated());

        return redirect()->route('purchase.index', ['item' => $item->id])->with('success', '送付先住所を更新しました');
    }
}
