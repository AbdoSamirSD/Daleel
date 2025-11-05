<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    //

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'address',
        'phone',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
