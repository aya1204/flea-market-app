<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * ログイン画面表示
     */
    public function index(Request $request)
    {
        return view('auth.login');
    }

    /**
     * 登録画面表示
     */
    public function register()
    {
        return view('auth.register');
    }
}