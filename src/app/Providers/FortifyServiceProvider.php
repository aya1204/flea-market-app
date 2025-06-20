<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use App\Http\Responses\CustomVerifyEmailViewResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

use function PHPUnit\Framework\returnSelf;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Fortify::authenticateUsing(function ($request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)){
                return $user;
            }

            return null;
        });

        // メール認証画面を表示
        Route::get('/email/verify', function () {
            $user = auth()->user();
            return view('auth.verify-email');
        });

        // メール認証処理
        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect('/mypage/profile');
        })->middleware(['auth', 'signed'])->name('verification.verify');
    }
}
