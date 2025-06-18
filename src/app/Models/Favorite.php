<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'item_id'];

    // User↔︎Favoriteの関係（多対1）
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Item↔︎Favoriteの関係（多対1）
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
