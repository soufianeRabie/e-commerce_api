<?php

namespace App\Http\Controllers;

use App\Models\Guest_basket;
use App\Models\OnlineOrders;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\User_basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\isNan;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {

        $ipAddress = $request->getClientIp();
        $product_id = $request->input("product_id");
        $user_id = $request->input("user_id");
        $quantity = $request->input('quantity' ,1);
        $user = \App\Models\User::find($user_id);

        $productOrigin = Product::where('id',$product_id)->first();
        if(!$productOrigin)
        {
            return response()->json(['error' =>'product dont exist'],401);
        }

        if(!is_numeric($quantity))
        {
            return response()->json(["error"=>'quantity must be a number' , $quantity] , 500);
        }



            if(!$user)
            {
                $ProductToAdd = Guest_basket::where('product_id' ,$product_id)
                    ->where('ip_address' , $ipAddress)
                    ->first();
                if($ProductToAdd &&( $productOrigin->quantity < ($ProductToAdd->quantity + $quantity)))
                {
                    return response()->json(['error' =>'quantity must be less than or equal to product quantity'],401);
                }
                $product = $this->AddToGuestCart($product_id , $ipAddress , $quantity);
                return response()->json($product);
            }else{
                $ProductToAdd = User_basket::where('product_id' ,$product_id)
                    ->where('user_id' , $user->id)
                    ->first();
                if($ProductToAdd &&( $productOrigin->quantity < ($ProductToAdd->quantity + $quantity)))
                {
                    return response()->json(['error' =>'quantity must be less than or equal to product quantity' ],401);
                }
                $product = $this->AddToUserCart($product_id , $ipAddress , $user , $quantity);
                return response()->json($product);
            }
        }
    public function removeProductFromCart(Request $request)
    {
        $user_id = $request->input('user_id');
        $user = \App\Models\User::find($user_id);
        $product_id = $request->input('product_id');
        $ipAddress = $request->getClientIp();
        $quantity = $request->input('quantity');
        if(!is_numeric($quantity))
        {
            return response()->json(["error"=>'quantity must be a number' , "quantity"=>$quantity] , 500);
        }else
        {
            if($user)
            {
                $product = $this->removeFromUserCart($product_id , $user , $quantity);
            }else
            {
                $product = $this->removeFromGuestCart($product_id , $ipAddress ,$quantity);
            }
        }

        return response()->json(['product' => $product]);
    }
    public function addGuestCartToUser(Request $request)
    {
        $user = Auth::user();
        if($user)
        {
            $ipAdress = $request->getClientIp();
            $cartGuest = Guest_basket::where('ip_address' , $ipAdress)->get();
            foreach ($cartGuest as $item){
                $this->AddToUserCart($item->product_id, $ipAdress , $user , $item->quantity);
            }
            Guest_basket::where('ip_address' , $ipAdress)->delete();
            return response()->json(true);
        }
    }

    public function getCart(Request $request)
    {
        $cart = $request->session()->get('cart' ,[]);
        return response()->json(['cart' => $cart]);
    }
    public function GuestCart (Request $request)
    {
        $ipAddress = $request->getClientIp();
        $cart = $this->getGuestCart($ipAddress);
        return response()->json(['cart'=>$cart] );
    }
    public function UserCart (Request $request)
    {
       $user = Auth::user();
     if($user)
     {
         $cart = $this->getUserCart($user);
         return response()->json(['cart'=>$cart] );
     }
     return response()->json("failed get user cart" , 500);
    }
    public function getUserOrders (Request $request)
    {
        $user = Auth::user();
        if($user)
        {
            $userOrders  = $user->orders()->with('product')->get();
            return response()->json(['userOrders'=>$userOrders , $user]);
        }
        return response()->json('error get orders' , 401);
    }

//    public function getUserOrders(Request $request)
//    {
//        $user = Auth::user();
//        if ($user) {
//            $userOrders = $user->orders()->with('product')->get();
//            return response()->json(['userOrders' => $userOrders, 'user' => $user]);
//        }
//        return response()->json('error get orders', 401);
//    }
//

    private  function AddToUserCart ($product_id , $ipAddress , $user , $quantity = 1)
    {
        $product = User_basket::where('product_id' ,$product_id)
            ->where('user_id' , $user->id)
            ->first();
        if($product)
        {
            $product->increment('quantity' , $quantity);
        }else
        {
            $product= User_basket::create([
                "id"=>$product_id,
                'ip_address' =>$ipAddress,
                'product_id'=>$product_id,
                'quantity'=> $quantity,
                "user_id"=>$user->id
            ]);
        }

        return $product;
    }

    private function createOrder( $user , $item , $ipAddress  , $originProduct , $onlineOrder)
    {

        $discount = config('globals.discount');

        return  Order::create([
            "email"=>$user->email,
            "product_id"=>$item->product_id,
            "ip_address"=>$ipAddress,
            "discount"=>$discount,
            "price"=>$originProduct->price,
            "totalPrice"=>$item->quantity * $originProduct->price ,
            "quantity"=>$item->quantity,
            "user_id"=>$user->id,
            "payment_method"=>"online",
            "status"=>"confirmed",
            "OnlineOrderId"=>$onlineOrder->id,
        ]);
    }

    private function manageOrder($user , $ipAddress)
    {

        $userCart = $this->getUserCart($user);


        $discount = $userCart->count()>14 ? 7 : $userCart->count() * 0.5;
        $totalAmount = $this->totalAmount($userCart);

      $onlineOrder =   OnlineOrders::create([
            'status'=>'confirmed',
            'user_id'=>$user->id,
            "amount"=>$totalAmount,
            "TotalDiscount"=>$discount
        ]);
//        return response($userCart);
        foreach ($userCart as $item)
        {
//            return response()->json($userCart)
            $quantity = $item->quantity;
            $originProduct = Product::where('id' , $item->product_id)
                ->first();

            (new OrderController)->MinusQuantityOfProductWhenPaymentComplete($item , $originProduct);
            $originProduct->decrement('quantity' , $quantity);
            {
                 $this->createOrder($user , $item , $ipAddress   , $originProduct , $onlineOrder);
                 $item->delete();
            }

        }
    }
    public function removeUserCartAfterPayment(Request $request )
    {
        $user = Auth::user();
        $ipAddress = $request->getClientIp();
        if($user)
        {

         return     $this->manageOrder($user , $ipAddress);
            $user->basket()->delete();
            return response()->json( true)  ;
        }
        return response()->json(false);
    }
    private  function AddToGuestCart ($product_id , $ipAddress , $quantity = 1 )
    {
       $product= Guest_basket::where('product_id',$product_id)
           ->where('ip_address',$ipAddress)
           ->first();
        if($product)
        {
            $product->increment('quantity' , $quantity);
        }
        else{
            $product=  Guest_basket::create([
                'ip_address'=>$ipAddress,
                'product_id'=>$product_id,
                'quantity'=> $quantity
            ]);
        }
        return $product;
        }

        private function removeFromGuestCart($product_id , $ipAddress  , $quantity =1)
        {
            $product = Guest_basket::where( 'product_id' , $product_id)
                ->where( 'ip_address' , $ipAddress)
                ->first();

            if(($product && $product->quantity === 1 )|| ($quantity >= $product->quantity))
            {
                $product->delete();
            }else
            {
                $product->decrement('quantity' , $quantity);
            }
            return $product;
        }
    private function removeFromUserCart($product_id , $user , $quantity =1)
    {
        $product = User_basket::where( 'product_id' , $product_id)
            ->where( 'user_id' , $user->id)
            ->first();

        if(($product && $product->quantity === 1 )|| ($quantity >= $product->quantity))
        {
            $product->delete();
        }else
        {
            $product->decrement('quantity' , $quantity);
        }

        return $product;
    }

        public function getUserCart ($user)
        {
           return User_basket::where('user_id', $user->id)->get();
        }
    private function getGuestCart ($ipAddress)
    {
        return Guest_basket::where('ip_address',$ipAddress)->get();
    }

    private function MinusQuantityOfProduct($product , $quantityToMinus)
    {
         $product->decrement('quantity',$quantityToMinus);
    }


    public static function  totalAmount($Basket)
    {
        $total = 0 ;

        foreach ($Basket as $item)
        {
            $product = Product::where('id',$item->product_id)->first();
            $total +=( $product->price * $item->quantity);
        }

        return $total ;
    }
}






