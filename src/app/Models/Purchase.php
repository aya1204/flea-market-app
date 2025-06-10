<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    //テーブル名の指定
    protected $table = 'purchases';

    //ユーザーとの多対1の関係
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //アイテムとの多対1の関係
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
