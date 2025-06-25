<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * 商品一覧取得、マイリスト一覧取得、商品検索機能、商品詳細情報取得、いいね機能、コメント送信機能のテスト
 */
class ItemTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * 商品一覧ページ表示
     */
    public function testGuestCanViewRecommendlist()
    {
        // 商品作成
        $item = \App\Models\Item::factory()->create(['title' => 'テスト商品']);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスと画面内に商品名が表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }
}
