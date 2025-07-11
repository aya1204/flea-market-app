<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // 例外をそのままスローせずにハンドルさせる
        $this->withExceptionHandling();
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
     * 名前が入力されていない場合のバリデーションテスト
     */
    public function testRegisterFailsWhenNameIsEmpty()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'testabc@example.com',
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
            'email' => 'testabc@example.com',
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
            'email' => 'testabc@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /**
     * パスワードと確認用パスワードが一致しない場合のバリデーションテスト
     */
    public function testRegisterFailsWhenPasswordConfirmationDoesNotMatch()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'testabc@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    /**
     * 正常に会員登録できる場合のテスト
     */
    public function testUserCanCreate()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'testabc@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]); // 会員登録処理

        // 登録後にログインページにリダイレクト
        $response->assertRedirect(route('profile.edit'));

        // 登録したユーザーを取得
        $user = \App\Models\User::where('email', 'testabc@example.com')->first();

        // ログインされているか
        $this->assertAuthenticatedAs($user);
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
     * メールアドレスが入力されていない場合のバリデーションテスト
     */
    public function testLoginFailsWhenEmailIsEmpty()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
        $this->assertGuest(); // ログイン失敗時に誤って認証されないようチェック
    }

    /**
     * パスワードが入力されていない場合のバリデーションテスト
     */
    public function testLoginFailsWhenPasswordIsEmpty()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'abc@example.com',
            'password' => '',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
        $this->assertGuest(); // ログイン失敗時に誤って認証されないようチェック
    }

    /**
     * 登録していないメールアドレスやパスワードでログインした場合のバリデーションテスト
     */
    public function testLoginFailsWithInvalidCredentials()
        {
            // 正しいユーザー作成
            $user = \App\Models\User::factory()->create([
                'email' => 'login@example.com',
                'password' => bcrypt('password123'),
            ]);

            $invalidCases = [
            ['email' => 'wrong1@example.com', 'password' => 'password123'], // メールアドレスが間違っている場合
            ['email' => 'login@example.com', 'password' => 'wrongpass'], // パスワードが間違っている場合
            ['email' => 'wrong3@example.com', 'password' => 'wrongpass'], // どちらも間違っている場合
            ];

            foreach ($invalidCases as $case) {
                $response = $this->from('/login')->post('/login', $case);
                $response->assertRedirect('/login');
                $response->assertSessionHasErrors([
                    'email' => 'ログイン情報が登録されていません。'
                ]);
                $this->assertGuest(); // ログイン失敗時に誤って認証されないようチェック
            }
        }

    /**
     * ログイン処理テスト
     */
    public function testUserCanLogin()
    {
        // 事前にユーザーを作成
        $user = \App\Models\User::factory()->create([
            'email' => 'loginuser' . uniqid() . '@example.com', // 毎回ユニークに（被らないように）
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertRedirect('/'); // ログイン後に商品一覧ページにリダイレクト
        $this->assertAuthenticatedAs($user);
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
        $this->assertGuest(); // ログアウトされているか
    }
}
