<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionMessage;
use App\Http\Requests\TransactionMessageRequest;
use App\Models\TransactionReview;
use App\Http\Requests\ReviewRequest;
use App\Models\Item;
use App\Mail\ReviewRequestMail;

class TransactionController extends Controller
{
    // 取引チャット画面表示
    public function show($transactionId)
    {
        // 取引データを探す
        $transaction = Transaction::find($transactionId);

        // 見つからない場合は仮のデータを設定
        if (!$transaction) {
            $transaction = new Transaction();
            $transaction->purchase_user_id = auth()->id(); // 現在のユーザーを購入者として設定
            $transaction->seller_user_id = 1; // 出品者ID
            $transaction->status = 'in_progress';
        }

        $currentUserId = auth()->id();
        $isBuyer = $transaction->purchase_user_id;
        $isSeller = $transaction->seller_user_id;
        // $user = auth()->user();

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

        // 取引中のアイテムを取得
        $items = Item::whereHas('transaction', function ($query) use ($currentUserId) {
            $query->where('seller_user_id', $currentUserId)
                ->orWhere('purchase_user_id', $currentUserId);
        })->get();

        return view('transaction.message', [
                'transaction' => $transaction,
                'isBuyer' => $isBuyer,
                'isSeller' => $isSeller,
                'buyerHasReviewed' => $buyerHasReviewed,
                'sellerHasReviewed' => $sellerHasReviewed,
                'items' => $items,
                'isBuyerLoggedIn' => $isBuyerLoggedIn, // 取引完了ボタンの表示に使用
        ]);
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
        if ($reviewerId === $transaction->purchase_user_id || $reviewerId === $transaction->seller_user_id) {
            $transaction->update(['status' => 'completed']);
        }

        return redirect()->route('items.index')->with('message', '評価を送信しました');
    }


    // public function storeReview(ReviewRequest $request, Transaction $transaction)
    // {
    //     // 評価した人（購入者）
    //     $reviewerId = auth()->id();
    //     // 評価された人（出品者）
    //     $revieweeId = $request->reviewee_id;

    //     // 二重投稿防止
    //     $alreadyReviewed = TransactionReview::where('transaction_id', $transaction->id)
    //         ->where('reviewer_id', $reviewerId)
    //         ->exists();

    //     // もし二重投稿したらエラーメッセージを表示する
    //     if ($alreadyReviewed) {
    //         return redirect()->route('items.index')->with('error', 'この取引はすでに評価済みです。');
    //     }

    //     // レビュー登録
    //     TransactionReview::create([
    //         'transaction_id' => $transaction->id,
    //         'reviewer_id' => $reviewerId,
    //         'reviewee_id' => $revieweeId,
    //         'rating' => $request->rating,
    //     ]);

    //     if ($reviewerId === $transaction->purchase)
    //     // 出品者→購入者への評価（両者完了済み→完了）
    //     if ($reviewerId === $transaction->seller_user_id) {
    //         $transaction->update(['status' => 'completed']);
    //     }
    //     $review = new TransactionReview();
    //     $review->transaction_id = $transaction->id;
    //     $review->reviewer_id = auth()->id(); // 評価を行うユーザー
    //     $review->reviewee_id = $request->reviewee_id; // 評価されるユーザー
    //     $review->rating = $request->rating;
    //     $review->save();

    //     return redirect()->route('items.index')->with('message', '評価を送信しました');
    // }
    // public function storeBuyerReview(ReviewRequest $request, Transaction $transaction)
    // {
    //     // 購入者が出品者を評価（星１〜５）
    //     $transaction->purchase_user_id = $request->rating;

    //     // データベースに保存する
    //     $transaction->save();

    //     return redirect()->route('items.index')->with('message', '取引評価を送信しました');
    // }

    // public function storeSellerReview(ReviewRequest $request, Transaction $transaction)
    // {
    //     // 出品者が購入者を評価（星１〜５）
    //     $transaction->seller_user_rating = $request->rating;

    //     // データベースに保存する
    //     $transaction->save();

    //     return redirect()->route('items.index')->with('message', '取引評価を送信しました');
    // }
}
