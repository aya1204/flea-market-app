<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // 無効な payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // 署名の検証失敗
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // イベント処理
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // セッションに保存した商品IDを取得
            $item_id = $session->metadata->item_id;
            $user_id = $session->metadate->purchase_user_id;
            $postal_code = $session->metadata->postal_code;
            $address = $session->metadata->address;
            $building = $session->metadata->building;

            $item = Item::find($item_id);

            if ($item && !$item->is_sold) {
                $item->update([
                    'is_sold' => true,
                    'purchase_user_id' => $user_id,
                    'postal_code' => $postal_code,
                    'address' => $address,
                    'building' => $building,
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
