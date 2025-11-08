<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class ProfileController extends Controller
{
    /**
     * プロフィール情報を表示（mypage含む）
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        // 平均評価を計算して渡す
        $averageRating = $user->averageRating();

        $tab = $request->query('tab', 'sell');

        $allTransactions = Transaction::where('seller_user_id', $user->id)
            ->orWhere('purchase_user_id', $user->id)
            ->with('messages')
            ->get();

        $transactionTabUnread = $allTransactions->sum(function ($t) use ($user) {
            return $t->messages->where('user_id', '!=', $user->id)->where('is_read', false)->count();
        });

        if ($tab === 'buy') {
            $items = $user->purchases()->with('item')->get();
        } elseif ($tab === 'sell') {
            $items = $user->itemsForSale()->with('transaction.messages')->get();
        } elseif ($tab === 'transaction') {
            $transactions = Transaction::where(function ($q) use ($user) {
                $q->where('seller_user_id', $user->id)
                    ->orWhere('purchase_user_id', $user->id);
            })
                ->where('status', Transaction::STATUS_IN_PROGRESS)
                ->with(['item', 'messages'])
                ->get()
                ->sortByDesc(fn($t) => optional($t->messages->first())->created_at);

            $items = $transactions->map(fn($t) => $t->item)->filter();
        } else {
            $items = collect();
        }

        if ($request->query('status') === 'success' && $request->query('session_id')) {
            session()->flash('success', '商品を購入しました。');
        }

        return view('profile.mypage', compact('user', 'tab', 'items', 'transactionTabUnread', 'averageRating'));
    }

    /**
     * プロフィール編集ページ
     */
    public function edit()
    {
        $user = auth()->user();
        return view('profile.mypage_profile', compact('user'))->with('success', 'プロフィールを更新しました！');
    }

    /**
     * プロフィール編集処理
     */
    public function update(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $user = User::findOrFail(auth()->id());

        //バリデーション済みデータ取得
        $image_data = $profileRequest->validated();
        $address_data = $addressRequest->validated();

        //プロフィール画像のアップロード
        if ($profileRequest->hasFile('image')) {
            $path = $profileRequest->file('image')->store('public/images');
            $image_data['image'] = basename($path);
        }

        //住所情報の更新
        $user->fill(array_merge($image_data, $address_data));
        $user->save();

        return redirect()->route('items.index', ['tab' => 'recommend'])->with('success', 'プロフィールを更新しました');
    }

    /**
     * プロフィール：購入済み商品
     */
    public function purchasedItem()
    {
        $user = Auth::user();
        $items = $user->purchases;
        return view('items.mylist', compact('items'));
    }

    /**
     * プロフィール：出品済み商品
     */
    public function soldItem()
    {
        $user = Auth::user();
        $items = $user->sales;
        return view('items.mylist', compact('items'));
    }
}
