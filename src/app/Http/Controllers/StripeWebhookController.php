<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;


class StripeWebhookController extends Controller
{
    /**
     * Stripeからの通知を受け取って購入完了処理をする場所
     */
    public function handle(Request $request)
    {
        // Stripeから送られてきたデータ（購入情報など）をそのまま受け取る
        $payload = $request->getContent();

        // Stripeから送られてくる印（署名）を受け取る（本当に購入されているか）
        $sigHeader = $request->header('Stripe-Signature');

        // Stripeの管理画面で決めた秘密のカギを読み込む（安全か確認するため）
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            // 送られてきたデータと署名、秘密のカギを使って
            // 本当にStripeから送られたものかチェックしている
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            Log::error('Webhook payload error: ' . $e->getMessage());
            // 無効な payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook signature error: ' . $e->getMessage());
            // 署名の検証失敗
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // イベント処理
        if ($event->type === 'checkout.session.completed') {
            // 支払いを終えたときの情報を取り出す
            $session = $event->data->object;

            // セッションに保存した商品IDを取得
            $item_id = $session->metadata->item_id;
            $user_id = $session->metadata->user_id;
            $postal_code = $session->metadata->postal_code;
            $address = $session->metadata->address;
            $building = $session->metadata->building;

            // 商品をデータベースから探す
            $item = Item::find($item_id);

            // 商品があって、まだ売れてなかったら
            // 売れたことにして住所や購入者の情報も更新する
            if ($item && !$item->is_sold) {
                $item->update([
                    'purchase_user_id' => $user_id,
                    'postal_code' => $postal_code,
                    'address' => $address,
                    'building' => $building,
                ]);

                // すでにこの商品と購入者の組み合わせで取引が存在するか確認
                $existingTransaction = Transaction::where('item_id', $item_id)
                    ->where('purchase_user_id', $user_id)
                    ->first();
                // 取引が存在しない場合のみ新しいTransactionレコードを作成
                if (!$existingTransaction) {
                    Transaction::create([
                        'item_id' => $item_id,
                        'seller_user_id' => $item->seller_user_id,
                        'purchase_user_id' => $user_id,
                        'status' => 'in_progress', // 購入時点で取引進行中に設定
                    ]);
                }
            }
        }

        // 処理が成功したことをStripeに返す
        return response()->json(['status' => 'success']);
    }
}
