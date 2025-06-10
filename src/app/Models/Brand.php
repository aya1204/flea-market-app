<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    // brand↔︎item 1対1の関係
    public function brand()
    {
        return $this->hasOne(Brand::class);
    }
}
