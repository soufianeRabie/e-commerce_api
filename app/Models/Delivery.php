<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        "ip_address",
        "status",
        "userId",
        "city",
        "amount",
        "first_name",
        "last_name",
        "email",
        "phone",
        "address",
        "TotalDiscount",
    ];


    public function orders(){
       return  $this->hasMany(Order::class  , "deliveriesId" ,"id")->with('product');
    }
}
