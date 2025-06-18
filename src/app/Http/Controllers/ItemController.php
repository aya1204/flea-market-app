<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

/**
 * アイテムコントローラー
 */
class ItemController extends Controller
{
    /**
     * 商品一覧ページ表示
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $items = collect(); // 空のコレクション
        $show_message = false;

        // セッションからキーワードを取得
        $keyword = session('search_keyword');

        // mylistタブの場合
        if ($tab === 'mylist') {
            // ログイン済みかチェック
            if (!auth()->check()) {
                $show_message = true;
            } else {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                $query = $user->favorites()->with('categories');

                if (!empty($keyword)) {
                    $query->where('title', 'like', '%' . $keyword . '%');
                }
                $items = $query->get();
            }
            // 4. recommendタブ（デフォルト）
        } else {
            $query = Item::query();

            // ログインしているかチェック
            if (auth()->check()) {
                // 自分が出品した商品を除く
                $query->where('seller_user_id', '!=', auth()->id());
            }

            if (!empty($keyword)) {
                $query->where('title', 'like', '%' . $keyword . '%');
            }

            $items = $query->get();
        }

        return view('items.mylist', compact('items', 'tab', 'show_message'));
    }
    /**
     * お気に入り追加
     */
    public function favorite(Item $item)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // お気に入り追加
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->favorites()->attach($item->id);

        return redirect()->back()->with('message', 'お気に入りに追加しました');
    }

    /**
     * お気に入り解除
     */
    public function unfavorite(Item $item)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $user->favorites()->detach($item->id);

        return redirect()->back()->with('message', 'お気に入りを解除しました');
    }
}