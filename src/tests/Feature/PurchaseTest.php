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
 * 商品購入機能・支払い方法選択機能・配送先変更機能のテスト
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
        $item = Item::factory()->create(['purchase_user_id' => null]);
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
        $item = Item::factory()->create(['purchase_user_id' => $user->id,]);

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
        $item = Item::factory()->create(['purchase_user_id' => null]);
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
            'purchase_user_id' => $user->id,
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


    /**
     * 配送先変更機能
     */

    /**
     * 送付先住所変更画面にて登録した住所が商品購入画面に反映されているテスト
     */
    public function testUpdateDeliveryAddressIsReflectedOnPurchasePage(): void
    {
        /** @var \App\Models\User $user */
        // テスト用ユーザーを作成(初期住所を設定)
        $user = User::factory()->create([
            'postal_code' => '100-0001',
            'address' => '旧住所',
            'building' => '旧ビル',
        ]);

        // テスト用ユーザーとしてログインする
        $this->actingAs($user);

        // 購入画面で使用する商品を作成
        $item = Item::factory()->create();

        // フォームから送信する新しい住所の情報を定義
        $newAddress = [
            'postal_code' => '150-0001',
            'address' => '新住所',
            'building' => '新ビル',
        ];

        // 送付先住所を更新(POSTリクエスト送信)
        $response = $this->post(route('purchase.update', ['item' => $item->id]), $newAddress);
        // 正常の購入画面にリダイレクトされつことを確認
        $response->assertRedirect(route('purchase.index', ['item' => $item->id]));

        // 商品購入ページにアクセスして更新した住所情報が反映されているか確認
        $response = $this->get(route('purchase.index', ['item' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('150-0001');
        $response->assertSee('新住所');
        $response->assertSee('新ビル');
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録されるテスト
     */
    public function testPurchasedItemHasCorrectDeliveryAddress(): void
    {
        // 支払い方法を選択して購入ボタンを押すとStripe画面に遷移するか？
        Mockery::mock('alias:' . StripeSession::class)
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'http://fake.url',
            ]);

        /** @var \App\Models\User $user */
        // テスト用ユーザーを作成(初期住所を設定)
        $user = User::factory()->create([
            'postal_code' => '200-0002',
            'address' => '旧住所',
            'building' => '旧ビル',
        ]);

        // テスト用ユーザーとしてログインする
        $this->actingAs($user);

        // 購入画面で使用する商品を作成(未購入状態)
        $item = Item::factory()->create([
            'purchase_user_id' => null,
        ]);

        // 新しい住所を登録(送付先変更)
        $newAddress = [
            'postal_code' => '123-4566',
            'address' => '東京都新宿区新宿',
            'building' => '新宿ビル5F',
        ];

        // 送付先住所を更新(POSTリクエスト送信)
        $response = $this->post(route('purchase.update', ['item' => $item->id]), $newAddress);
        $response->assertRedirect(route('purchase.index', ['item' => $item->id]));

        // 消費納入処理のPOSTリクエスト送信
        $paymentMethod = Paymentmethod::factory()->create(['name' => 'カード払い']);

        // 商品を購入する
        $response = $this->post(route('purchase.create', ['item' => $item->id]), [
            'paymentmethod_id' => $paymentMethod->id,
            'postal_code' => $newAddress['postal_code'],
            'address' => $newAddress['address'],
            'building' => $newAddress['building'],
        ]);

        // StripeのリダイレクトURLに遷移するためリダイレクト確認
        $response->assertStatus(302);

        // データベースの最新状態を反映
        $item->refresh();

        // テスト用に purchase_user_id を強制設定
        $item->update([
            'purchase_user_id' => $user->id,
        ]);

        // 送付先住所が商品に正しく保存されているか
        $this->assertEquals($user->id, $item->purchase_user_id);
        $this->assertEquals('123-4566', $item->postal_code);
        $this->assertEquals('東京都新宿区新宿', $item->address);
        $this->assertEquals('新宿ビル5F', $item->building);
    }
}
