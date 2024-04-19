<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class complaint extends Model
{
    use HasFactory;


    protected  $fillable = [
        'type',
        'user_id',
        'message',
        'deliveryId',
        'orderId',
        'address_ip'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->hasOne(User::class , 'id' , 'user_id');
    }
}
