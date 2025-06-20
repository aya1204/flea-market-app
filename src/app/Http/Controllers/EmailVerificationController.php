<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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

    /**
     * メール認証処理
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/mypage/profile');
        }
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('profile.edit')->with('success', 'メール認証が完了しました。プロフィールを設定してください。');
    }
}
