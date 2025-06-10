<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\User;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Routing\Loader\Configurator\CollectionConfigurator;

/**
 * アイテムコントローラー
 */
class ItemController extends Controller
{
    /**
     * 商品一覧ページ表示
     * 
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $items = collect(); // 空のコレクション
        $showMessage = false;

        // セッションからキーワードを取得
        $keyword = session('search_keyword');

        // mylistタブの場合
        if ($tab === 'mylist') {
            // ログイン済みかチェック
            if (!auth()->check()) {
                $showMessage = true;
            } else {
                $query = auth()->user()->favorites()->with('categories');

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

        return view('items.mylist', compact('items', 'tab', 'showMessage'));
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
        auth()->user()->favorites()->attach($item->id);

        return redirect()->back()->with('message', 'お気に入りに追加しました');
    }

    /**
     * お気に入り解除
     */
    public function unFavorite(Item $item)
    {
        auth()->user()->favorites()->detach($item->id);

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
        $showMessage = false;
        $items = collect(); // 空のコレクション
        $keyword = null;

        // 1. キーワードが入力されている場合
        if ($request->has('keyword')) {
            $keyword = $request->input('keyword');

            if ($keyword !== '') {
                session(['search_keyword' => $keyword]); // セッションに保存（共通キー）
            } else {
                session()->forget('search_keyword'); // 空白検索ならセッション削除
            }
        }
        // 2. リクエストにキーワードがない場合、セッションから取得
        $keyword =$keyword ?? session('search_keyword');

        // 3. mylistタブの場合
        if ($tab === 'mylist') {
            // ログイン済みかチェック
            if (!auth()->check()) {
                $showMessage = true;
                $items = collect();
            } else {
                $query = auth()->user()->favorites()->with('categories');

                // 商品名を検索（部分一致）
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

            // 商品名を検索（部分一致）
            if (!empty($keyword)) {
                $query->where('title', 'like', '%' . $keyword . '%');
            }

            $items = $query->get();

        }
        return view('items.mylist', compact('items', 'tab', 'showMessage'));
    }
}