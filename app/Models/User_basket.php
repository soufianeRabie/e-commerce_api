<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_basket extends Model
{
    use HasFactory;

    protected $table = "user_basket";
    protected $fillable =[
        "product_id",
        "ip_address",
        "quantity",
        "user_id"
    ];


    // User_basket.php

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity');
    }


}
