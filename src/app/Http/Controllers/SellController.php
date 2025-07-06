<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class SellController extends Controller
{
    /**
     * 出品画面表示
     */
    public function index()
    {
        $categories = Category::all(); // 全カテゴリを取得
        return view('sell.sell', compact('categories')); // resources/views/sell/sell.blade.php を表示
    }

    /**
     * 出品処理
     */
    public function create(ExhibitionRequest $request)
    {
        $user = Auth::user();

        $image_path = $request->file('image')->store('images', 'public');

        $item = new Item();
        $item->seller_user_id = $user->id;
        $item->title = $request->input('title');
        $item->brand_id = $request->input('brand_id');
        $item->description = $request->input('description');
        $item->price = $request->input('price');
        $item->condition_id = $request->input('condition_id');
        $item->image = $image_path;
        $item->save();

        // カテゴリ名を探して見つかったら多対多リレーションを通じて中間テーブルに保存
        $categoryIds = $request->input('categories', []);

        if (!empty($categoryIds)) {
            $item->categories()->attach($categoryIds);
        }

        return redirect()->route('items.index')->with('success', '商品を出品しました。');
    }
}
