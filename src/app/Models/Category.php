<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    // category↔︎item 多対多の関係
    public function items()
    {
        return $this->belongsToMany(Item::class, 'category_item');
    }
}
