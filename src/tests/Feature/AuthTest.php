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

}
