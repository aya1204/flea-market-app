<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Item;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;


/**
 * アイテムコントローラー
 */
class ItemController extends Controller
{
    /**
     * 共通処理をまとめる
     */
    private function getFilteredItems($tab, $keyword)
    {
        $items = collect(); // 空のコレクション
        $show_message = false;

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

        return [
            'items' => $items,
            'show_message' => $show_message,
        ];
    }

    /**
     * 商品一覧ページ表示
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = session('search_keyword');

        $result = $this->getFilteredItems($tab, $keyword);

        $items = $result['items'];
        $show_message = $result['show_message'];

        return view('items.mylist', [
        'items' => $items,
        'tab' => $tab,
        'show_message' => $show_message,
    ]);
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

    /**
     * 商品詳細画面
     */
    public function show(Item $item)
    {
        $item->load(['categories', 'condition', 'comments.user', 'favoritedByUsers', 'brand']);
        return view('items.show', compact('item'));
    }

    /**
     * コメント投稿
     */
    public function comment(CommentRequest $request, Item $item)
    {

        $item->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->route('items.show', $item)->with('success', 'コメントを投稿しました');
    }

    /**
     * 検索機能
     * キーワードをセッションに保存、タブを移動してもキーワードが保持される
     * 空白で検索するとセッションに保存されたキーワードがリセットされる
     */
    public function search(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        // $show_message = false;
        $keyword = null;

        // 1. キーワードが入力されている場合
        if ($request->has('item_name')) {
            $keyword = $request->input('item_name');

            if ($keyword !== '') {
                session(['search_keyword' => $keyword]); // セッションに保存（共通キー）
            } else {
                session()->forget('search_keyword'); // 空白検索ならセッション削除
            }
        }
        // 2. リクエストにキーワードがない場合、セッションから取得
        $keyword = $keyword ?? session('search_keyword');

        $result = $this->getFilteredItems($tab, $keyword);

        return view('items.mylist', [
            'items' => $result['items'],
            'tab' => $tab,
            'show_message' => $result['show_message'],
        ]);
    }
}
