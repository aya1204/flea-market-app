<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // itemsテーブルと多対1の関係
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
