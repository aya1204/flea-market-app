<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class SellTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /**
     * 商品出品画面にて必要な情報（カテゴリ、商品の状態、商品名、商品の説明、販売価格）が保存できるテスト
     */
    public function testUserCanRegisterItemWithValidData()
    {
        // 1. ストレージのモック(テスト用にstorageを仮想化)
        Storage::fake('public');
        /** @var \App\Models\User $user */

        // 2. ユーザー、カテゴリ、状態、ブランドを作成
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $category = Category::factory()->create();
        $condition = Condition::factory()->create();
        $brand = Brand::factory()->create();

        // 3. ダミー画像を生成
        $image = UploadedFile::fake()->image('test.jpg');

        // 4. ログインして、フォームデータ送信
        $this->actingAs($user);

        $response = $this->post(route('sell.create'), [
            'title' => 'テスト商品',
            'description' => 'これはテスト商品です。',
            'price' => 3000,
            'condition_id' => $condition->id,
            'brand_id' => $brand->id,
            'categories' => [(string)$category->id],
            'image' => $image,
        ]);
        $this->assertTrue(auth()->check());

        // 5. マイページ出品した商品タブにリダイレクト
        $response->assertRedirect(route('mypage', ['tab' => 'sell']));

        // 6. アップロードされた画像ファイルが保存されているか確認
        Storage::disk('public')->assertExists('images/' . $image->hashName());

        // 7. itemsテーブルに商品情報が登録されているか
        $this->assertDatabaseHas('items', [
            'title' => 'テスト商品',
            'description' => 'これはテスト商品です。',
            'price' => 3000,
            'condition_id' => $condition->id,
            'brand_id' => $brand->id,
            'seller_user_id' => $user->id,
            'image' => 'images/' . $image->hashName(), // パスが一致するかを確認
        ]);

        $item = Item::where('title', 'テスト商品')
            ->where('seller_user_id', $user->id)
            ->first();

        $this->assertNotNull($item);
        $this->assertDatabaseHas('category_item', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);
    }
}
