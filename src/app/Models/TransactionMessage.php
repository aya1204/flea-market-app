<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMessage extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'user_id',
        'message',
        'is_read',
        'image',
    ];

    // テーブル名の指定
    protected $table = 'transaction_messages';

    // このメッセージが紐づく取引(transaction)と多対1の関係
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    // メッセージを送ったユーザー（出品者or購入者）と多対1の関係
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
