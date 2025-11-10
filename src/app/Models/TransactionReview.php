<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment',
    ];

    // テーブル名の指定
    protected $table = 'transaction_reviews';

    // このメッセージが紐づく取引(transaction)と1対1の関係
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // レビューした人（出品者or購入者）と多対1の関係
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // レビューされた人（出品者or購入者）と多対1の関係
    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
