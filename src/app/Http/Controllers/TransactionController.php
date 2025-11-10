<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Http\Requests\TransactionMessageRequest;
use App\Models\TransactionReview;
use App\Http\Requests\ReviewRequest;
use App\Mail\ReviewRequestMail;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    // 取引チャット画面表示
    public function show($transactionId)
    {
        // 取引データを探す
        $transaction = Transaction::with('item', 'seller', 'purchase', 'messages.user')->findOrFail($transactionId);

        // 見つからない場合は仮のデータを設定
        if (!$transaction) {
            $transaction = new Transaction();
            $transaction->purchase_user_id = auth()->id(); // 現在のユーザーを購入者として設定
            $transaction->seller_user_id = 1; // 出品者ID
            $transaction->status = 'in_progress';
        }

        $currentUserId = auth()->id();
        if ($transaction->purchase_user_id !== $currentUserId && $transaction->seller_user_id !== $currentUserId) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // 未読メッセージを既読にする
        $transaction->messages()
            ->where('user_id', '!=', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // 取引に関連する商品を取得
        $item = $transaction->item;

        // 購入者かどうか判定
        $isBuyerLoggedIn = $currentUserId === $transaction->purchase_user_id;

        // 取引に関連する商品を取得（出品者を取得する方法）
        $item = $transaction->item;

        // 既に評価済みかどうかを判定
        $buyerHasReviewed = TransactionReview::where('transaction_id', $transaction->id)
            ->where('reviewer_id', $transaction->purchase_user_id)
            ->exists();
        $sellerHasReviewed = TransactionReview::where('transaction_id', $transaction->id)
            ->where('reviewer_id', $transaction->seller_user_id)
            ->exists();

        return view('transaction.message', [
                'transaction' => $transaction,
                'item' => $item,
                'isBuyer' => $transaction->purchase_user_id,
                'isSeller' => $transaction->seller_user_id,
                'buyerHasReviewed' => $buyerHasReviewed,
                'sellerHasReviewed' => $sellerHasReviewed,
                'isBuyerLoggedIn' => $isBuyerLoggedIn, // 取引完了ボタンの表示に使用
        ]);
    }

    // 取引メッセージ送信
    public function sendMessage(TransactionMessageRequest $request, $transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);

        if ($transaction->seller_user_id !== auth()->id() &&
            $transaction->purchase_user_id !== auth()->id()) {
                abort(403);
        }

        $data = [
            'transaction_id' => $transactionId,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_read' => false,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('transaction_images', 'public');
            $data['image'] = $path;
        }

        TransactionMessage::create($data);

        return redirect()->route('transaction.show', $transactionId)->withInput();
    }

    // 取引メッセージ削除
    public function deleteMessage($messageId)
    {
        $message = TransactionMessage::findOrFail($messageId);

        // 自分のメッセージか確認
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction = $message->transaction;
        $message->delete();

        return redirect()->route('transaction.show', $transaction->id)->with('success', 'メッセージを削除しました');
    }

    public function complete($transactionId)
    {
        // 取引データを取得
        $transaction = Transaction::findOrFail($transactionId);

        // 取引の関係あるユーザーか確認
        if (
            $transaction->seller_user_id !== auth()->id() &&
            $transaction->purchase_user_id !== auth()->id()
        ) {
            abort(403);
        }

        // 取引完了後、マイページへリダイレクト
        return redirect()->route('mypage', ['tab' => 'transaction'])->with('success', '取引が完了しました');
    }

    // 送信済みメッセージを編集する
    public function updateMessage(TransactionMessageRequest $request, $messageId)
    {
        $message = TransactionMessage::findOrFail($messageId);

        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $message->update([
            'message' => $request->message
        ]);

        return redirect()->back();
    }

    public function storeReview(ReviewRequest $request, Transaction $transaction)
    {
        // 評価した人（購入者）
        $reviewerId = auth()->id();

        // 評価される人（出品者または購入者）を設定
        $revieweeId = $transaction->purchase_user_id == $reviewerId
            ? $transaction->seller_user_id  // 購入者が評価している場合は出品者がreviewee
            : $transaction->purchase_user_id; // 出品者が評価している場合は購入者がreviewee

        // 二重投稿防止
        $alreadyReviewed = TransactionReview::where('transaction_id', $transaction->id)
            ->where('reviewer_id', $reviewerId)
            ->exists();

        // もし二重投稿したらエラーメッセージを表示する
        if ($alreadyReviewed) {
            return redirect()->route('items.index')->with('error', 'この取引はすでに評価済みです。');
        }

        // レビュー登録
        TransactionReview::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => $reviewerId,
            'reviewee_id' => $revieweeId,
            'rating' => $request->rating,
        ]);

        // 取引が完了した場合は、ステータスを更新
        if ($reviewerId === $transaction->purchase_user_id) {
            $seller = $transaction->seller;
            // メール送信
            Mail::to($seller->email)->send(new ReviewRequestMail($transaction));

            if ($reviewerId === $transaction->purchase_user_id || $reviewerId === $transaction->seller_user_id) {}

            return redirect()->route('items.index')->with('message', '評価を送信しました');
        }
    }
}
