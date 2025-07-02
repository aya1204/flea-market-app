<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class ProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /**
     * プロフィールページ出品した商品タブで必要な情報が表示されるテスト(プロフィール画像、ユーザー名、出品した商品)
     */
    public function testProfilePageDisplaysSellingItems()
    {
        /** @var \App\Models\User $user */
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'image' => 'images/test_user_icon.png',
        ]);

        // 出品商品を3件作成
        $sellingItems = Item::factory()->count(3)->create([
            'seller_user_id' => $user->id,
        ]);

        // ログインする
        $this->actingAs($user);

        // 出品した商品タブにアクセス
        $response = $this->get(route('mypage', ['tab' => 'sell']));

        $response->assertStatus(200);

        // ユーザー名とプロフィール画像が表示されているか
        $response->assertSee('テストユーザー');
        $response->assertSee($user->image);

        // 出品商品が表示されているか
        foreach ($sellingItems as $item) {
            $response->assertSee($item->title);
        }
    }


    /**
     * プロフィールページ購入した商品タブで必要な情報が表示されるテスト(プロフィール画像、ユーザー名、購入した商品)
     */
    public function testProfilePageDisplaysPurchasedItems(): void
    {
        /** @var \App\Models\User $user */
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'image' => 'images/test_user_icon.png',
        ]);

        // 購入商品を2件作成
        $purchasedItems = Item::factory()->count(2)->create([
            'purchase_user_id' => $user->id,
        ]);

        // ログイン
        $this->actingAs($user);

        // 購入した商品タブにアクセス
        $response = $this->get(route('mypage', ['tab' => 'buy']));

        $response->assertStatus(200);

        // ユーザー名とプロフィール画像が表示されているか
        $response->assertSee('テストユーザー');
        $response->assertSee($user->image);

        // 購入商品が表示されているか
        foreach ($purchasedItems as $item) {
            $response->assertSee($item->title);
        }
    }
}
