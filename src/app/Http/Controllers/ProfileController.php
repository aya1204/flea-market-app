<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * プロフィール情報を表示（mypage含む）
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $tab = $request->query('tab', 'sell');

        if ($tab === 'buy') {
            $items = $user->purchases;
        } elseif ($tab === 'sell') {
            $items = $user->itemsForSale;
        } else {
            $items = null;
        }

        if ($request->query('status') === 'success' && $request->query('session_id')) {
            session()->flash('success', '商品を購入しました。');
        }
        return view('profile.mypage', compact('user', 'tab', 'items'));
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
}