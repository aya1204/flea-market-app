<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * ログイン・新規登録・ログアウトのテストファイル
 */
class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /**
     * 会員登録画面表示テスト
     */
    public function testUserCanViewRegisterPage()
    {
        $response = $this->get('/register'); // 会員登録ページ

        $response->assertStatus(200);
        $response->assertSee('会員登録'); // ページ内に「会員登録」という文字があるか
    }

    /**
     * 名前が入力されていない場合のバリデーションテスト
     */
    public function testRegisterFailsWhenNameIsEmpty()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /**
     * メールアドレスが入力されていない場合のバリデーションテスト
     */
    public function testRegisterFailsWhenEmailIsEmpty()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /**
     * パスワードが入力されていない場合のバリデーションテスト
     */
    public function testRegisterFailsWhenPasswordIsEmpty()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }


    /**
     * パスワードが7文字以下の場合のバリデーションテスト
     */
    public function testRegisterFailsWhenPasswordIsTooShort()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * 会員登録処理テスト
     */
    public function testUserCanCreate()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]); // 会員登録処理

        $response->assertRedirect('/email/verify'); // 登録後にメール認証ページにリダイレクト
        $this->assertAuthenticated(); // ログインされているか
    }


    /**
     * ログインページ表示テスト
     */
    public function testUserCanViewLoginPage()
    {
        $response = $this->get('/login'); // ログインページ

        $response->assertStatus(200);
        $response->assertSee('ログイン'); //ページ内に「ログイン」という文字があるか
    }

    /**
     * ログイン処理テスト
     */
    public function testUserCanLogin()
    {
        // 事前にユーザーを作成
        $user = \App\Models\User::factory()->create([
            'email' => 'loginuser@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'loginuser@example.com',
            'password' => 'password'
        ]);

        $response->assertRedirect('/'); // ログイン後に商品一覧ページにリダイレクト
        $response->assertAuthenticatedAs($user);
    }


    /**
     * ログアウト処理テスト
     */
    public function testUserCanLogout()
    {
        /** @var \App\Models\User $user */
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user); // ログイン状態にする

        $response = $this->post('/logout');

        $response->assertRedirect('/'); // ログアウト後、商品一覧ページ（未ログイン）にリダイレクト
        $response->assertGuest(); // ログアウトされているか
    }
}
