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
     * ログインページ表示テスト
     */
    public function testUserCanViewLoginPage()
    {
        $response = $this->get('/login'); // ログインページ

        $response->assertStatus(200);
        $response->assertSee('ログイン'); //ページ内に「ログイン」という文字があるか
    }


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

}
