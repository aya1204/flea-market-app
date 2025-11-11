<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


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

        // 処理開始ログ (デバッグ用)
        Log::info('Stripe Webhook received.');

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
            Log::error('Webhook signature error (400): ' . $e->getMessage());
            // 署名の検証失敗
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // イベント処理
        if ($event->type === 'checkout.session.completed') {
            // 支払いを終えたときの情報を取り出す
            $session = $event->data->object;
            // StripeセッションIDを取得
            $session_id = $session->id;

            // セッションに保存した商品IDを取得
            // item_idとuser_idは整数にする
            $item_id = intval($session->metadata->item_id);
            $user_id = intval($session->metadata->user_id);
            $postal_code = $session->metadata->postal_code;
            $address = $session->metadata->address;
            $building = $session->metadata->building;

            // 商品をデータベースから探す
            $item = Item::find($item_id);

            Log::info('Processing checkout.session.completed for Item ID: ' . $item_id . ', User ID: ' . $user_id);

            // 商品があって、まだ売れてなかったら
            // 売れたことにして住所や購入者の情報も更新する
            if ($item && !$item->is_sold) {
                // 必須IDのチェック★
                if (empty($item->seller_user_id) || empty($user_id)) {
                    Log::error('DB integrity error (500): Missing Seller ID or Purchase User ID.');
                    return response()->json(['error' => 'Missing required IDs'], 500);
                }
                // StripeセッションIDで二重作成を防止
                try {
                    // DBトランザクションを開始
                    DB::beginTransaction();

                    // 取引がすでに存在しないか確認し、なければ作成
                    $transaction = Transaction::firstOrCreate(
                        ['stripe_session_id' => $session_id],
                        [ // 作成データ
                            'item_id' => $item_id,
                            'seller_user_id' => $item->seller_user_id,
                            'purchase_user_id' => $user_id,
                            'status' => 'in_progress', // 購入時点で取引進行中に設定
                        ]
                    );

                    // 取引が新規作成された場合、商品の情報を更新
                    if ($transaction->wasRecentlyCreated) {
                        $item->update([
                            'purchase_user_id' => $user_id,
                            'postal_code' => $postal_code,
                            'address' => $address,
                            'building' => $building,
                            'is_sold' => true, // 二重の購入を防ぐ
                        ]);
                        Log::info('New Transaction created and Item updated successfully.');
                    } else {
                        Log::warning('Transaction already exists for session ID: ' . $session_id . '. Item update skipped.');
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Transaction DB error (500): ' . $e->getMessage());
                    // データベースエラーが発生した場合もStripeにエラーを返す
                    return response()->json(['error' => 'Database operation failed'], 500);
                }
            } else {
                Log::warning('Item not found or already sold. Item ID: ' . $item_id);
            }

        // 処理が成功したことをStripeに返す
        return response()->json(['status' => 'success']);
    }
}
}