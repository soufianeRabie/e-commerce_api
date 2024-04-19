<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineOrders extends Model
{
    use HasFactory;


    protected  $fillable = [
        'status',
        'TotalDiscount',
        'amount',
        'user_id',
    ];


    public function orders(){
        return  $this->hasMany(Order::class  , "OnlineOrderId" ,"id")->with('product');
    }
    public function user(){
        return  $this->hasMany(User::class  , 'id' , 'user_id');
    }
}
