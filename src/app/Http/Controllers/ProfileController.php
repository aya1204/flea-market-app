<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
