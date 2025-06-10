<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'item_id', 'comment'];
    // user↔︎comment 多対1の関係
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // items↔︎comments 多対1の関係
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
