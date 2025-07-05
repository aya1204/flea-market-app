<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


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

    /**
     * ユーザー登録処理
     */
    public function create(RegisterRequest $request)
    {

        // テストならメール認証スキップ後プロフィール編集画面へ遷移
        if (app()->environment('testing')) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // テスト環境でも自動ログインさせる
            Auth::login($user);

            return redirect()->route('profile.edit');
        }

        // 本番環境はログイン後プロフィール編集画面へ
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect()->route('profile.edit');
    }

    /**
     * ログイン処理
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // メール認証済みかチェック
            if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'メール認証が完了していません。メールを確認して認証を完了させてください。'
                ]);
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
        ]);
    }

    /**
     * ログアウト処理
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}