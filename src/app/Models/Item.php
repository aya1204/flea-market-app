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
}
