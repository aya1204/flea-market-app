<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'price', 'image', 'description', 'paymentmethod_id', 'purchase_user_id', 'is_sold'];

    public static $rules = array(
        'image' => 'required|mimes:jpeg,png',
        'title' => 'required',
        'description' => 'required|max:255',
        'price' => 'required|integer|min:0',
    );

    // テーブル名の指定
    protected $table = 'items';

    // item↔︎user 多対1の関係
    public function sellerUser()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }

    public function purchaseUser()
    {
        return $this->belongsTo(User::class, 'purchase_user_id');
    }

    // item↔︎favorite 1対多の関係
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // item↔︎category 多対多の関係
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_item');
    }

    // itemが売り切れかチェック
    public function isSold(): bool
    {
        return $this->is_sold;
    }

    // item↔︎condition 1対多の関係
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    // item↔︎brand 1対1の関係
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // item↔︎comment 1対多の関係
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    }
