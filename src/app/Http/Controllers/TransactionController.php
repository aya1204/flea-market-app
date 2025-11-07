<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Http\Requests\TransactionMessageRequest;
use App\Models\TransactionReview;
use App\Http\Requests\ReviewRequest;
use App\Models\Item;
use App\Mail\ReviewRequestMail;
use Illuminate\Support\Facades\Mail;

class TransactionController extends Controller
{
    // 取引チャット画面表示
    public function show($transactionId)
    {
        // $transaction = Transaction::findOrFail($transactionId);
        $transaction = Transaction::with(['seller', 'purchase', 'item', 'messages.user'])->findOrFail($transactionId);
        $currentUserId = auth()->id();

        $isBuyer = $transaction->purchase_user_id === $currentUserId;
        $isSeller = $transaction->seller_user_id === $currentUserId;
        // $user = auth()->user();

        // 既に評価済みかどうかを判定
        $buyerHasReviewed = TransactionReview::where('transaction_id', $transaction->id)
            ->where('reviewer_id', $transaction->purchase_user_id)
            ->exists();
        $sellerHasReviewed = TransactionReview::where('transaction_id', $transaction->id)
            ->where('reviewer_id', $transaction->seller_user_id)
            ->exists();

        // サイドバー用（その他の取引）
        $items = Item::whereHas('transaction', function ($query) use ($currentUserId) {
            $query->where(function ($q) use ($currentUserId) {
                $q->where('seller_user_id', $currentUserId)
                    ->orWhere('purchase_user_id', $currentUserId);
            })->where('status', Transaction::STATUS_IN_PROGRESS);
        })->get();

        return view('transaction.message', compact(
            'transaction', 'isBuyer', 'isSeller',
            'buyerHasReviewed', 'sellerHasReviewed', 'items'
        ));
        // $transactions = Transaction::where(function($query) use ($user) {
        //     $query->where('seller_user_id', $user->id)
        //         ->orWhere('purchase_user_id', $user->id);
        // })
        // ->with(['item', 'messages' => function($query) {
        //     $query->latest()->limit(1);
        // }])
        // ->get()
        // ->sortByDesc(function($transaction) {
        //     return $transaction->messages->first()->created_at ?? $transaction->created_at;
        // });

        // $currentTransaction = $transactionId
        //     ? Transaction::with(['item', 'seller', 'purchase', 'messages.user'])->findOrFail($transactionId)
        //     : $transactions->first();

        // if ($currentTransaction) {
        //     $currentTransaction->messages()
        //         ->where('user_id', '!=', $user->id)
        //         ->where('is_read', false)
        //         ->update(['is_read' => true]);
        // }

        // // 取引に紐づいた商品をまとめて$itemsにセットする
        // $items = $transactions->pluck('item');

        // return view('transaction.message', compact('transactions', 'currentTransaction', 'items'));
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

    // 取引完了
    public function complete($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);

        // 取引の関係あるユーザーか確認
        if ($transaction->seller_user_id !== auth()->id() &&
            $transaction->purchase_user_id !== auth()->id()) {
                abort(403);
            }

            $transaction->update(['status' => 'completed']);

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

    // 評価する
    public function storeReview(ReviewRequest $request, Transaction $transaction)
    {
        // 評価した人（購入者）
        $reviewerId = auth()->id();
        // 評価された人（出品者）
        $revieweeId = $transaction->seller_user_id === $reviewerId
            ? $transaction->purchase_user_id
            : $transaction->seller_user_id;

        // 評価を保存
        TransactionReview::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => $reviewerId,
            'reviewee_id' => $revieweeId,
            'rating' => $request->rating,
        ]);

        // 購入者 → 出品者への評価（出品者にメール通知）
        if ($reviewerId === $transaction->purchase_user_id) {
            $seller = $transaction->seller;
            Mail::to($seller->email)->send(new ReviewRequestMail($transaction));
        }
        // 出品者 → 購入者への評価（両者完了済み → 完了）
        if ($reviewerId === $transaction->seller_user_id) {
            $transaction->update(['status' => Transaction::STATUS_COMPLETED]);
        }

        return redirect()->route('items.index')->with('message', '評価を送信しました');
    }
}
