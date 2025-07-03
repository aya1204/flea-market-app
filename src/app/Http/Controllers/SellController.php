<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class SellController extends Controller
{
    /**
     * 出品画面表示
     */
    public function index()
    {
        return view('sell.sell'); // resources/views/sell/sell.blade.php を表示
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
        // カテゴリーの中間テーブルへ保存(多対多リレーション)
        $item->categories()->attach($request->input('categories'));
        return redirect()->route('mypage', ['tab' => 'sell'])->with('success', '商品を出品しました。');
    }
}
