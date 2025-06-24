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

}
