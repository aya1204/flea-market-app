<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Paymentmethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Stripe\Checkout\Session as StripeSession;

/**
 * 商品購入機能テスト
 */
class PurchaseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    /**
     * 「購入する」ボタンを押下すると購入が完了するテスト
     */
    public function testUserCanPurchaseItemAndRedirectToStripe()
    {
        /** @var \App\Models\User $user */
        // ユーザーと商品と支払い方法を作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => false]);
        $paymentMethod = Paymentmethod::factory()->create([
            'name' => 'カード払い'
        ]);

        // 支払い方法を選択して購入ボタンを押すとStripe画面に遷移するか？
        Mockery::mock('alias:' . StripeSession::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://fake.stripe.session/checkout',
            ]);

        // ユーザーとしてログインし、POSTリクエスト
        $response = $this->actingAs($user)->post(route('purchase.create', ['item' => $item->id]), [
            'postal_code' => '111-1111',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'paymentmethod_id' => $paymentMethod->id,
        ]);

        // Stripeへのリダイレクトが行われることを確認
        $response->assertRedirect('https://fake.stripe.session/checkout');

        // ユーザーの住所が更新されたことを確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'postal_code' => '111-1111',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
        ]);
    }

    /**
     * 購入した商品は商品一覧画面にて「Sold」と表示されるテスト
     */
    public function testPurchasedItemShowsSoldLabelOnItemList()
    {
        /** @var \App\Models\User $user */
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create(['is_sold' => true]);

        // 商品一覧ページにアクセス
        $response = $this->actingAs($user)->get(route('items.index'));

        // 「Sold」の表示があることを確認
        $response->assertStatus(200);
        $response->assertSee('Sold');
    }
}
