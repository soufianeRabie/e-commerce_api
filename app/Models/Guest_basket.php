<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest_basket extends Model
{
    use HasFactory;


    protected $table = "guest_basket";
   protected $fillable =[
       "product_id",
       "ip_address",
       "quantity",
   ];
}
