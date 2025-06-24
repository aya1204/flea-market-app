<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
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
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => app()->environment('testing') ? now() : null,
        ]);

        Auth::login($user);
        return redirect()->route('profile.edit');
    }

    /**
     * ログイン処理
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません。',
            'password' => 'ログイン情報が登録されていません。',
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