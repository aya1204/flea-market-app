<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Brand;

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
    use RefreshDatabase;

    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * 商品一覧ページ表示
     */

    /**
     * recommendタブで全商品を見ることができるテスト
     */
    public function testGuestCanViewAllItemsInRecommendTab()
    {
        /** @var \Illuminate\Support\Collection $items */
        // 複数件商品作成(タイトルを個別に設定)
        $items = collect([
            \App\Models\Item::factory()->create(['title' => 'テスト商品1']),
            \App\Models\Item::factory()->create(['title' => 'テスト商品2']),
            \App\Models\Item::factory()->create(['title' => 'テスト商品3']),
        ]);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // ステータスと画面内に商品名が表示されていることを確認
        $response->assertStatus(200);
        foreach ($items as $item) {
            $response->assertSee($item->title);
        }
    }

    /**
     * ゲストがrecommendタブで購入済み商品はSoldと表示されるテスト
     */
    public function testGuestCanSeeSoldLabelInRecommendTab()
    {
        // 購入済み商品(purchase_user_idが設定されている)
        $item = Item::factory()->create([
            'title' => '購入済み商品',
            'is_sold' => true,
            'purchase_user_id' => User::factory()->create()->id, //実在するユーザーのIDを指定
        ]);

        // recommendタブにアクセス
        $response = $this->get('/?tab=recommend');

        // 「Sold」が表示されていることを確認
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }

    /**
     * ログイン済みユーザーがrecommendタブで購入済み商品はSoldと表示されるテスト
     */
    public function testAuthenticatedUserCanSeeSoldLabelInRecommendTab()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーを作成
        $user = User::factory()->create();

        // 購入済み商品(purchase_user_idが設定されている)
        $item = Item::factory()->create([
            'title' => '購入済み商品',
            'is_sold' => true,
            'purchase_user_id' => $user->id,
        ]);

        // お気に入りに追加
        $user->favorites()->attach($item->id);

        // recommendタブにアクセス
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
        $user = User::factory()->create();

        // ログインユーザーが出品した商品(非表示)
        Item::factory()->create([
            'seller_user_id' => $user->id,
            'title' => '自分の商品',
        ]);

        // 他人が出品した商品(表示)
        Item::factory()->create([
            'seller_user_id' => User::factory()->create()->id,
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
        $user = User::factory()->create();
        $item = Item::factory()->create([
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
     * ログイン済みユーザーがmylistタブで購入済み商品はSoldと表示されるテスト
     */
    public function testAuthenticatedUserCanSeeSoldLabelInMylistTab()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーを作成
        $user = User::factory()->create();

        // 購入済み商品(purchase_user_idが設定されている)
        $soldItem = Item::factory()->create([
            'title' => '購入済み商品',
            'is_sold' => true,
            'purchase_user_id' => $user->id,
        ]);

        // お気に入りに追加
        $user->favorites()->attach($soldItem->id);

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
        $user = User::factory()->create();

        // ログインユーザーが出品した商品(非表示)
        Item::factory()->create([
            'seller_user_id' => $user->id,
            'title' => '自分の商品',
        ]);

        // 他人が出品した商品(表示)
        $otherItem = Item::factory()->create([
            'seller_user_id' => User::factory()->create()->id,
            'title' => '他人の商品',
        ]);

        // 他人の商品をお気に入りに追加
        $user->favorites()->attach($otherItem);

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
     * 商品検索機能
     */

    /**
     * 商品名で部分一致検索ができるテスト
     */
    public function testCanSearchItemsByPartialMatchInTitle()
    {
        // 商品を複数作成（部分一致のものと一致しないもの）
        Item::factory()->create(['title' => '青森のりんご']);
        Item::factory()->create(['title' => 'りんごジュース']);
        Item::factory()->create(['title' => '完熟バナナ']);

        // 検索リクエスト（セッションにキーワードを保存する形式ならPOSTで）
        $response = $this->get('/search?item_name=りんご');


        // 部分一致する商品は表示される
        $response->assertSee('青森のりんご');
        $response->assertSee('りんごジュース');

        // 一致しない商品は表示されない
        $response->assertDontSee('完熟バナナ');
    }

    /**
     * recommendタブで部分一致検索した結果がmylistにも保持されているテスト
     */
    public function testPartialSearchWorksInRecommendAndMylistTabs()
    {
        /** @var \App\Models\User $user */

        // ログインユーザー作成
        $user = User::factory()->create();

        // 商品を複数作成(部分一致のものと一致しないもの)
        $item1 = Item::factory()->create(['title' => '青森のりんご']);
        $item2 = Item::factory()->create(['title' => 'りんごジュース']);
        $item3 = Item::factory()->create(['title' => '完熟バナナ']);

        $user->favorites()->detach(); // 念のため全削除
        // ユーザーのお気に入りにitem1,item3を登録
        $user->favorites()->attach([$item1->id, $item3->id]);

        // 1.recommendタブで検索(部分一致)
        $responseRecommend = $this->actingAs($user)->get('/search?tab=recommend&item_name=りんご');

        $responseRecommend->assertStatus(200);

        // 検索結果「りんご」を含む商品が表示される(item1とitem2)
        $responseRecommend->assertSee('青森のりんご');
        $responseRecommend->assertSee('りんごジュース');

        // 含まれない商品は表示されない(item3)
        $responseRecommend->assertDontSee('完熟バナナ');

        // 2.mylistタブで「りんご」を検索(部分一致)
        $responseMylist = $this->actingAs($user)->get('/search?tab=mylist&item_name=りんご');

        $responseMylist->assertStatus(200);

        // mylistはお気に入りのみ表示→お気に入りはitem1,item3なのでtitleに「りんご」を含むitem1だけ表示される
        $responseMylist->assertSee('青森のりんご');
        $responseMylist->assertDontSee('りんごジュース'); // お気に入りじゃないので非表示
        $responseMylist->assertDontSee('完熟バナナ');

        // リダイレクト先で内容を確認するため、再リクエスト
        $response = $this->get('/?tab=recommend');

        // 部分一致する商品は表示される
        $response->assertSee('青森のりんご');
        $response->assertSee('りんごジュース');

        // 一致しない商品は表示されない
        $response->assertDontSee('完熟バナナ');
    }

    /**
     * 商品詳細情報取得
     */

    /**
     * 必要な情報が表示される(商品画像、商品名、ブランド名、価格、いいね数、コメント数、商品説明、商品情報(カテゴリ、商品の状態)、コメント数、コメントしたユーザー情報、コメント内容)
     */
    public function testItemDetailDisplaysAllNecessaryInformation()
    {
        // ユーザーとカテゴリと商品を作成
        $user = User::factory()->create(['name' => 'テストユーザー', 'image' => 'test_user_icon.jpg']);
        $category = Category::factory()->create(['name' => 'テストカテゴリー']);
        $condition = Condition::factory()->create(['name' => '良好']);
        $brand = Brand::factory()->create(['name' => 'ブランド名']);

        $item = Item::factory()->create([
            'title' => 'テスト商品',
            'price' => 3000,
            'description' => 'これはテスト用の商品説明文です。',
            'image' => 'images/test.jpg',
            'brand_id' => $brand->id,
            'condition_id' => $condition->id,
        ]);

        // カテゴリー登録
        $item->categories()->attach($category->id);

        // お気に入り追加(いいね)
        $item->favoritedByUsers()->attach($user->id);

        // コメントを追加
        $item->comments()->create([
            'user_id' => $user->id,
            'comment' => 'これはテストコメントです。',
        ]);

        // 商品詳細画面(/item/{item})にアクセス
        $response = $this->get("/item/{$item->id}");

        $response->assertStatus(200);

        // 商品に関する必要な情報が画面に表示されているか確認
        $response->assertSee('テスト商品');
        $response->assertSee('¥3,000');
        $response->assertSee('ブランド名');
        $response->assertSee('これはテスト用の商品説明文です');
        $response->assertSee('テストカテゴリー');
        $response->assertSee('良好');

        // いいね・コメント数
        $response->assertSee('class="favorite_count"', false);
        $response->assertSee('class="comment_count"', false);

        // コメント情報
        $response->assertSee('コメント(1)');
        $response->assertSee('テストユーザー');
        $response->assertSee('これはテストコメントです。');

        // 画像が表示されているか
        $response->assertSee('storage/images/test.jpg');
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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // お気に入り登録前は0件
        $this->assertEquals(0, $item->favoritedByUsers()->count());

        //ログインしてお気に入り追加処理を実行
        $response = $this->actingAs($user)->post("/item/{$item->id}/favorite");

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
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // お気に入り登録
        $user->favorites()->attach($item->id);

        // 再度リレーションをロードしておく(特にfavorites)
        $user->load('favorites');

        // ログイン状態で商品詳細ページにアクセス
        $response = $this->actingAs($user)->get("/item/{$item->id}");

        $response->dump();

        // 色付きアイコン(例: class="favorited_icon")が表示されていることを確認
        $response->assertSee('class="favorited_icon"', false);
    }

    /**
     * ログインユーザーがお気に入り解除できるテスト
     */
    public function testUserCanUnfavoriteAnItem()
    {
        /** @var \App\Models\User $user */

        // ログインユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

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
