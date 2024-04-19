<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory , SoftDeletes;

    protected  $fillable =[
        "title","description","price","isSold" , "oldPrice","rating","quantity"
    ];

    protected $casts = [
        'isSold' => 'boolean',
        'oldPrice' => 'decimal:2',
    ];

    public function images()
    {
        return $this->hasMany(product_images::class );
    }


}
