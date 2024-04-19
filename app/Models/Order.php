<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable =[
        "email",
        "product_id",
        "ip_address",
        "discount",
        "quantity",
        "user_id",
        "deliveriesId",
        "payment_method",
        "status",
        "totalPrice",
        "price",
        "OnlineOrderId",
    ];


    public function user()
    {
        return $this->hasOne(User::class , 'id' , 'user_id');
    }
    public function product()
    {
        return $this->hasOne(Product::class , 'id' , 'product_id');
    }
    public function delivery()
    {
        return $this->hasOne(Delivery::class , 'id' , 'deliveriesId');
    }

    public function onlineOrder()
    {
        return $this->hasOne(OnlineOrders::class , 'id' , 'OnlineOrdersId');
    }

}

