<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public static $rules = array(
        'name' => 'required|string|max:255',
    );

    // itemsテーブルと多対1の関係
    public function item()
    {
        return $this->hasMany(Item::class);
    }

}
