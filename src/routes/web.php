<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

//会員登録ページ表示
Route::get('/register', [AuthController::class, 'register'])->middleware('guest')->name('register');

//会員登録処理
Route::post('/register', [AuthController::class, 'create'])->middleware('guest');

