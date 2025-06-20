<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Paymentmethod;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\AddressRequest;

class PurchaseController extends Controller
{
    /**
     * 商品購入ページ表示
     */
    public function index(Item $item)
    {
        $paymentmethods = Paymentmethod::all();
        $item = Item::find($item->id);

        $selectedPaymentMethod = request()->query('paymentmethod_id');

        // 支払い方法の名前を取得
        $methodName = optional($paymentmethods->firstWhere('id', $selectedPaymentMethod))->name;

        return view('purchase.purchase', compact('item', 'paymentmethods', 'user', 'selectedPaymentMethod', 'methodName'));
    }


    /**
     * 商品購入処理
     */
    public function create(PurchaseRequest $request, Item $item)
    {

        // すでに売れているかチェック
        if ($item->is_sold) {
            return redirect()->route('items.show', $item)->with('error', 'この商品はすでに購入されています。');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. ユーザーの住所を更新
        $user->update([
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        // 2. Stripeセッション作成
        // Stripe APIキーをセット
        Stripe::setApiKey(config('services.stripe.secret'));

        // 支払い方法の取得
        $paymentmethod_id = $request->input('paymentmethod_id');
        $paymentmethod = Paymentmethod::find($paymentmethod_id);


        // 支払いタイプ判定
        $payment_type = empty($paymentmethod) ?: $paymentmethod->name;

        $common_metadata = [
            'item_id' => (string) $item->id,
            'user_id' => (string) $user->id,
            'postal_code' => (string) $request->input('postal_code'),
            'address' => (string) $request->input('address'),
            'building' => (string) $request->input('building'),
        ];

        // Checkoutセッション作成
        if ($payment_type === 'コンビニ払い') {
            $session = Session::create([
                'payment_method_types' => ['konbini'],
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
                'success_url' => route('mypage', ['tab' => 'buy']) . '?session_id={CHECKOUT_SESSION_ID}&status=success', // 成功後プロフィール画面へ
                'cancel_url' => route('purchase.index', ['item' => $item->id]),
                'metadata' => $common_metadata,
            ]);
            return redirect($session->url);
        } elseif ($payment_type === 'カード払い') {
            $session = Session::create([
                'payment_method_types' => ['card'],
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
                'success_url' => route('items.show', ['item' => $item->id]) . '?status=success', // 成功後商品詳細画面へ
                'cancel_url' => route('purchase.index', ['item' => $item->id]),
                'metadata' => $common_metadata,
            ]);
            return redirect($session->url);
        } else {
            return redirect()->back()->with('error', '支払い方法が選択されていません。');
        }
    }

    /**
     * 住所変更画面を表示
     */
    public function address(Item $item)
    {
        $user = Auth::user();
        return view('purchase.delivery_address_edit', compact('user', 'item'));
    }

    /**
     * 住所変更保存
     */
    public function update(AddressRequest $request, Item $item)
    {

        // 購入者を設定し、商品側にも住所を保存
        /** @var \App\Models\Item $item */
        $item->update([
            'purchase_user_id' => Auth::id(),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        // 購入者のプロフィール住所も更新
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update($request->validated());

        return redirect()->route('purchase.index', ['item' => $item->id])->with('success', '送付先住所を更新しました');
    }
}
