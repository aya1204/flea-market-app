<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ログイン画面表示設定
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // 会員登録画面表示設定
        Fortify::registerView(function () {
            return view('auth.register');
        });
    }
}
