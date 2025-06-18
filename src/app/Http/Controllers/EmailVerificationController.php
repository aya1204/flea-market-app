<?php

namespace App\Http\Controllers;

/**
 * メール認証のコントローラー
 */
class EmailVerificationController extends Controller
{
    /**
     * メール認証画面表示
     */
    public function index()
    {
        return view('auth.verify-email');
    }
}
