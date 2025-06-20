<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ProfileController;

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

    // 新規登録後、メール認証画面を表示
    Route::get('/email/verify', [EmailVerificationController::class, 'index'])
        ->middleware('auth')->name('verification.notice');

    // メール認証処理
    Route::get(
        '/email/verify/{id}/{hash}', // どのユーザーが対象か識別
        EmailVerificationController::class
    )
        ->middleware(['auth', 'signed', 'throttle:6,1']) // ログインしているか、改ざんされていないか、1時間に６回までのアクセス制限
        ->name('verification.verify');

    //プロフィール設定（認証とメール認証が必要）
    Route::middleware(['auth', 'verified'])->group(function () {
        // プロフィール設定画面表示
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        // プロフィール設定完了処理
        Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    //マイページ（タブ切り替え：出品した商品｜購入した商品）
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage');

    // お気に入り登録
    Route::post('/item/{item}/favorite', [ItemController::class, 'favorite'])->name('favorites.store');
    // お気に入りから外す
    Route::delete('/item/{item}/favorite', [ItemController::class, 'unfavorite'])->name('favorites.destroy');

    // コメントを送信する
    Route::post('/item/{item}/comment', [ItemController::class, 'comment'])->name('item.comment');
});


//会員登録ページ表示
Route::get('/register', [AuthController::class, 'register'])->middleware('guest')->name('register');

//会員登録処理
Route::post('/register', [AuthController::class, 'create'])->middleware('guest');

// ログイン画面表示
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->middleware('guest')->name('login');

// ログイン処理
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

//ログアウト機能
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//商品一覧ページ表示
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 検索機能（誰でもアクセス可、キーワード保持）
Route::get('/search', [ItemController::class, 'search'])->name('items.search');

// 商品詳細ページ表示
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
