<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 認証
Route::middleware(['auth'])->group(function () {
    // 認証のみ必要なページ

    //出品画面
    Route::get('/sell', [SellController::class, 'index'])->name('sell');
    // 出品処理
    Route::post('/sell', [SellController::class, 'create'])->name('sell.create');

    //マイページ（タブ切り替え：プロフィール｜購入｜売却）
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');

    // お気に入り登録
    Route::post('/item/{item}/favorite', [ItemController::class, 'favorite'])->name('favorites.store');
    // お気に入りから外す
    Route::delete('/item/{item}/favorite', [ItemController::class, 'unfavorite'])->name('favorites.destroy');

    // 商品購入画面へ
    Route::get('/purchase/{item}', [PurchaseController::class, 'index'])->name('purchase.index');
    // 商品購入処理
    Route::post('/purchase/{item}', [PurchaseController::class, 'create'])->name('purchase.create');
    // 送付先住所変更画面（アイコンなし）
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'address'])->name('purchase.address');
    // 送付先住所変更保存
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'update'])->name('purchase.update');

    // コメントを送信する
    Route::post('/item/{item}/comment', [ItemController::class, 'comment'])->name('item.comment');

    //プロフィール設定（認証とメール認証が必要）
    Route::middleware(['auth', 'verified'])->group(function () {
        // プロフィール設定画面表示
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.mypage_profile');
        // プロフィール設定完了処理
        Route::post('/profile/mypage_profile', [ProfileController::class, 'update'])->name('profile.update');
    });
});

// 商品詳細ページ
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');

//会員登録ページ表示
Route::get('/register', [AuthController::class, 'register'])->middleware('guest')->name('register');

//会員登録
Route::post('/register', [AuthController::class, 'create'])->middleware('guest');
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
->middleware('guest');

// ログイン画面表示
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');

//商品一覧ページ表示
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 検索機能（誰でもアクセス可、キーワード保持）
Route::get('/search', [ItemController::class, 'search'])->name('items.search');


//ログアウト機能
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');
