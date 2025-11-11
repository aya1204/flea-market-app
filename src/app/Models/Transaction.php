<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    protected $fillable = [
        'seller_user_id',
        'purchase_user_id',
        'item_id',
        'status',
    ];

    // テーブル名の指定
    protected $table = 'transactions';

    // 出品者(seller_user)と多対1の関係
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    // 購入者(purchase_user)と多対1の関係
    public function purchase()
    {
        return $this->belongsTo(User::class, 'purchase_user_id');
    }

    // 取引対象の商品(item)と1対多の関係
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // 取引に紐づくメッセージ(messages)と1対多の関係
    public function messages() {
        return $this->hasMany(TransactionMessage::class);
    }

    // 取引の紐づくレビュー（review）と1対1の関係
    public function reviews()
    {
        return $this->hasOne(TransactionReview::class);
    }

    // 未読メッセージをカウントする関数
    public function unreadCountForUser($userId)
    {
        return $this->messages()->where('user_id', '!=', $userId)->where('is_read', false)->count();
    }
}
