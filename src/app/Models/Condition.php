<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;
    protected $fillable = ['condition'];

    public static $rules = array(
        'condition' => 'required',
    );

    // condition↔︎item 多対1の関係
    public function item()
    {
        return $this->hasMany(Item::class);
    }

}
