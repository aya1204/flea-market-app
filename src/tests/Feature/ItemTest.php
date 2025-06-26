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

     /**
      * ログインしていないユーザーがrecommendタブで商品一覧を見ることができる
      */
    public function testGuestCanViewRecommendTab()
    {
        // 商品作成
        $item = \App\Models\Item::factory()->create(['title' => 'テスト商品']);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスと画面内に商品名が表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
    }

    /**
     * ログイン済みユーザーがrecommendタブで自分が出品した商品以外見ることができる
     */
    public function testLoggedInUserDoesNotSeeOwnItemsInRecommendTab()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーを作成
        $user = \App\Models\User::factory()->create();

        // ログインユーザーが出品した商品(非表示)
        \App\Models\Item::factory()->create([
            'seller_user_id' => $user->id,
            'title' => '自分の商品',
        ]);

        // 他人が出品した商品(表示)
        \App\Models\Item::factory()->create([
            'seller_user_id' => \App\Models\User::factory()->create()->id,
            'title' => '他人の商品',
        ]);

        // ログイン状態でrecommendタブにアクセス
        $response = $this->actingAs($user)->get('/?tab=recommend');

        // 自分の商品が表示されていない
        $response->assertDontSee('自分の商品');

        // 他人の商品は表示されている
        $response->assertSee('他人の商品');
    }
}
