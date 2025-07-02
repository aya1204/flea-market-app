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
     * 商品購入機能
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

        // ユーザーとしてログインし、購入処理を実行
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

    /**
     * 「プロフィール/購入した商品一覧」に追加されているテスト
     */
    public function testPurchasedItemAppearsInUserProfileBuyTab()
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

        // ユーザーとしてログインし、購入処理を実行
        $response = $this->actingAs($user)->post(route('purchase.create', ['item' => $item->id]), [
            'postal_code' => '111-1111',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'paymentmethod_id' => $paymentMethod->id,
        ]);

        // 商品の購入者IDとis_soldを手動で更新(Stripe経由の成功はこのテストでは再現できないため)
        $item->update([
            'purchase_user_id' => $user->id,
            'is_sold' => true,
        ]);

        // マイページの購入した商品タブにアクセス
        $response = $this->actingAs($user)->get(route('mypage', ['tab' => 'buy']));

        // 購入した商品が含まれていることを確認
        $response->assertStatus(200);
        $response->assertSee($item->title);
    }


    /**
     * 支払い方法選択機能
     */

    /**
     * 小計画面で変更が即時反映されるテスト
     */
    public function testPaymentMethodIsReflectedOnPurchasePage()
    {
        /** @var \App\Models\User $user */
        // ユーザーと商品を作成
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 支払い方法を2つ作成してデータベースに保存
        $paymentMethods = Paymentmethod::factory()->count(2)->create();

        // 作成した支払い方法2件の中から最初の1件を取り出す
        $selectedPaymentMethod = $paymentMethods->first();

        // ログインして購入画面を表示(支払い方法を指定する)
        $response = $this->actingAs($user)->get(route('purchase.index', [
            'item' => $item->id,
            'paymentmethod_id' => $selectedPaymentMethod->id,
        ]));

        $response->assertStatus(200);

        // ビューに選択した支払い方法の名前が含まれているか確認
        $response->assertSee($selectedPaymentMethod->name);
    }
}
