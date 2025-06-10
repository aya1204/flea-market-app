<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;

use function PHPUnit\Framework\returnSelf;

class ProfileController extends Controller
{
    // プロフィール情報を表示（mypage含む）
    public function index()
    {
        $user = auth()->user();
        $tab = request('tab', 'sell');

        if ($tab === 'buy') {
            $items = $user->purchases;
        } elseif ($tab === 'sell') {
            $items = $user->sales;
        } else {
            $items = null;
        }
        return view('profile.mypage', compact('user', 'tab', 'items'));
    }

    public function edit ()
    {
        $user = auth()->user();
        return view('profile.mypage_profile', compact('user'))->with('success', 'プロフィールを更新しました！');
    }

    public function update(ProfileRequest $profileRequest, AddressRequest $addressRequest)
    {
        $user = \App\Models\User::findOrFail(auth()->id());

        //バリデーション済みデータ取得
        $imageData = $profileRequest->validated();
        $addressData = $addressRequest->validated();

        //プロフィール画像のアップロード
        if ($profileRequest->hasFile('image')) {
            $path = $profileRequest->file('image')->store('public/images');
            $imageData['image'] = basename($path);
        }

        //住所情報の更新
        $user->fill(array_merge($imageData, $addressData));
        $user->save();

        return redirect()->route('items.index', ['tab' => 'recommend'])->with('success', 'プロフィールを更新しました');
    }

    public function purchasedItem()
    {
        $user = Auth::user();
        $items = $user->purchases;
        return view('items.mylist', compact('items'));
    }

    public function soldItem()
    {
        $user = Auth::user();
        $items = $user->sales;
        return view('items.mylist', compact('items'));
    }
}
