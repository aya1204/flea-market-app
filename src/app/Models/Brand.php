<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    // itemsテーブルと1対多の関係
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
