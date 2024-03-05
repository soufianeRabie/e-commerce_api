<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory , SoftDeletes;

    protected  $fillable =[
        "title","description","price","isSold" , "oldPrice","rating","image"
    ];

    protected $casts = [
        'isSold' => 'boolean',
        'oldPrice' => 'decimal:2',
    ];

}
