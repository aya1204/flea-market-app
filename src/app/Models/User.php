<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Item;
use App\Models\Transaction;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    // itemsテーブル(出品)と多対１の関係
    public function itemsForSale()
    {
        return $this->hasMany(Item::class, 'seller_user_id');
    }

    // itemsテーブル(購入)と多対1の関係
    public function purchases()
    {
        return $this->hasMany(Item::class, 'purchase_user_id');
    }

    // favoritesテーブルと多対多の関係
    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id')->withTimestamps();
    }

    // commentsテーブルと1対多の関係
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 出品者としての取引
    public function transactionsAsSeller()
    {
        return $this->hasMany(Transaction::class, 'seller_user_id');
    }

    // 購入者としての取引
    public function transactionsAsBuyer()
    {
        return $this->hasMany(Transaction::class, 'purchase_user_id');
    }

    // 出品・購入両方の取引をまとめて取得
    public function transactions()
    {
        return $this->transactionsAsSeller->merge($this->transactionsAsBuyer);
    }

    public function averageRating()
    {
        // 自分がレビューされた評価の平均を計算
        return $this->reviewsGiven()->avg('rating');
    }

    // ユーザーが受けたレビュー
    public function reviewsGiven()
    {
        return $this->hasMany(TransactionReview::class, 'reviewee_id');
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'postal_code',
        'address',
        'building',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
