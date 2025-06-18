<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'image',
        'description',
        'paymentmethod_id',
        'purchase_user_id',
        'is_sold',
        'brand_id',
        'condition_id',
        'seller_user_id',
    ];

    // テーブル名の指定
    protected $table = 'items';

    // 出品者(sellerUser)と多対1の関係
    public function sellerUser()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    // 購入者(purchaseUser)と多対1の関係
    public function purchaseUser()
    {
        return $this->belongsTo(User::class, 'purchase_user_id');
    }

    // favoritesテーブルと1対多の関係
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // categoriesテーブルと多対多の関係
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    // itemが売り切れかチェック
    public function isSold(): bool
    {
        return $this->is_sold;
    }

    // conditionsテーブルと多対1の関係
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    // brandsテーブルと多対1の関係
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
