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
      * ログインしていないユーザーがrecommendタブで商品一覧を見ることができるテスト
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
     * recommendタブで購入済み商品はSoldと表示されるテスト
     */
    public function testSoldItemIsLabeledSoldInRecommendTab()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーを作成
        $user = \App\Models\User::factory()->create();

        // 購入済み商品(purchase_user_idが設定されている)
        $item = \App\Models\Item::factory()->create([
            'title' => '購入済み商品',
            'purchase_user_id' => $user->id,
        ]);

        // お気に入りに追加
        $user->favorites()->attach($item->id);

        // mylistタブにアクセス
        $response = $this->actingAs($user)->get('/?tab=recommend');

        // 「Sold」が表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * ログイン済みユーザーがrecommendタブで自分が出品した商品以外見ることができるテスト
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

    /**
     * マイリスト一覧取得
     */

    /**
     * ログイン済みのユーザーはmylistタブでお気に入り商品を見ることができるテスト
     */
    public function testLoggedInUserCanViewFavoritesInMylistTab()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create([
            'title' => 'お気に入りの商品',
        ]);

        // お気に入りに登録
        $user->favorites()->attach($item->id);

        // ログインしてmylistタブにアクセス
        $response = $this->actingAs($user)->get('/?tab=mylist');

        $response->assertStatus(200);

        // ←商品名が表示されている確認
        $response->assertSee('お気に入りの商品');
    }

    /**
     * mylistタブで購入済み商品はSoldと表示されるテスト
     */
    public function testSoldItemIsLabeledSoldInMylist()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーを作成
        $user = \App\Models\User::factory()->create();

        // 購入済み商品(purchase_user_idが設定されている)
        $item = \App\Models\Item::factory()->create([
        'title' => '購入済み商品',
        'purchase_user_id' => $user->id,
        ]);

        // お気に入りに追加
        $user->favorites()->attach($item->id);

        // mylistタブにアクセス
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // 「Sold」が表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * ログイン済みユーザーがmylistタブで自分が出品した商品以外見ることができるテスト
     */
    public function testLoggedInUserDoesNotSeeOwnItemsInMylistTab()
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
        $response = $this->actingAs($user)->get('/?tab=mylist');

        // 自分の商品が表示されていない
        $response->assertDontSee('自分の商品');

        // 他人の商品は表示されている
        $response->assertSee('他人の商品');
    }

    /**
     * ログインしていない状態でmylistタブにアクセスするとメッセージが表示されるテスト
     */
    public function testGuestUserSeesMessageOnMylistTab()
    {
        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('マイリストを表示するにはログインしてください。');
    }

    /**
     * お気に入り機能
     */

    /**
     * ログインしているユーザーがお気に入り追加できるテスト
     */
    public function testUserCanAddItemToFavorites()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // お気に入り登録前は0件
        $this->assertEquals(0, $item->favoritedByUsers()->count());

        //ログインしてお気に入り追加処理を実行
        $response = $this->actingAs($user)->post("/item/{item->id}/favorite");

        // リダイレクト確認(通常は元のページへ)
        $response->assertRedirect();

        // お気に入り登録されたかデータベースで確認
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // お気に入り登録後は1件に増えているかチェック
        $this->assertEquals(1, $item->fresh()->favoritedByUsers()->count());
    }

    /**
     * お気に入り済みのアイコンの色が変化するテスト
     */
    public function testTheColorChangeOfTheFavoritedIcon()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // お気に入り登録
        $user->favorites()->attach($item->id);

        // ログイン状態で商品詳細ページにアクセス
        $response = $this->actingAs($user)->get("/item/{$item->id}/favorite");

        // 色付きアイコン(例: class="favorited_icon"))が表示されていることを確認
        $response->assertSee('class="favoritedicon"', false);
    }

    /**
     * ログインユーザーがお気に入り解除できるテスト
     */
    public function testUserCanUnfavoriteAnItem()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーと商品を作成
        $user = \App\Models\User::factory()->create();
        $item = \App\Models\Item::factory()->create();

        // 事前にお気に入り追加しておく
        $user->favorites()->attach($item->id);

        // お気に入り登録済みで1件
        $this->assertEquals(1, $item->favoritedByUsers()->count());

        // ログイン状態でお気に入り解除リクエストを送る
        $response = $this->actingAs($user)->delete("/item/{$item->id}/favorite");

        // リダイレクトの確認
        $response->assertRedirect();

        // favoritesテーブルから削除されていることを確認
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        // お気に入り解除後は0件に減っているかチェック
        $this->assertEquals(0, $item->fresh()->favoritedByUsers()->count());
    }
}
