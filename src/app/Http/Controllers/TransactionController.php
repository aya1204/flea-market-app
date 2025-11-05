<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionController extends Controller
{
    // 取引チャット画面表示
    public function show($transactionId = null)
    {
        $user = auth()->user();

        $transactions = Transaction::where(function($query) use ($user) {
            $query->where('seller_user_id', $user->id)
                ->orWhere('purchase_user_id', $user->id);
        })
        ->with(['item', 'messages' => function($query) {
            $query->latest()->limit(1);
        }])
        ->get()
        ->sortByDesc(function($transaction) {
            return $transaction->messages->first()->created_at ?? $transaction->created_at;
        });

        $currentTransaction = $transactionId
            ? Transaction::with(['item', 'seller', 'purchase', 'messages.user'])->findOrFail($transactionId)
            : $transactions->first();

        if ($currentTransaction) {
            $currentTransaction->messages()
                ->where('user_id', '!=', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        // 取引に紐づいた商品をまとめて$itemsにセットする
        $items = $transactions->pluck('item');

        return view('transaction.message', compact('transactions', 'currentTransaction', 'items'));
    }
}
