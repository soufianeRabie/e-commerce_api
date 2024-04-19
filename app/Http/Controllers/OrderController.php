<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Guest_basket;
use App\Models\Order;
use App\Models\TmepOrder;
use App\Models\User_basket;
use Illuminate\Http\Request;
use App\Http\Requests\AdminRequest;

 class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Orders = Order::with(['product' , 'user' , 'delivery' , 'onlineOrder'])->get();
        $Orders->makeHidden(["updated_at" , "deleted_at"]);

        return response()->json($Orders);
    }

    public function MinusQuantityOfProductWhenPaymentComplete( $product , $originProduct): void
    {

        $restQuantity = $originProduct->quantity - $product->quantity;
        foreach (User_basket::all() as $item)
        {
            if($item->product_id === $product->product_id )
            {
                if($restQuantity >0 )
                {
                      if($item->quantity > $restQuantity)
                      {
                          $item->quantity = $restQuantity;
                          $item->save();
                      }
                }else
                {
                      $item->delete();
                }

            }
        }

        foreach (Guest_basket::all() as $item)
        {
            if($item->product_id === $product->product_id )
            {
                if($restQuantity >0 )
                {
                    if($item->quantity > $restQuantity)
                    {
                        $item->quantity = $restQuantity;
                        $item->save();
                    }
                }else
                {
                    $item->delete();
                }

            }
        }
    }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request)
     {

         $userIpAddress = $request->getClientIp();
         $userId = $request->input("user" , null);
         $Basket = [];
         if(!$userId)
         {
             $Basket = Guest_basket::where('ip_address' , $userIpAddress);
         }else
         {
             $Basket = User_basket::where('user_id' , $userId);
         }

         foreach ($Basket as $item)
         {
             TmepOrder::create([
                 "product_id"=>$item->product_id
             ]);
         }

         $formFields = $request->post();
         if($userId)
         {
             $formFields['user']= $request->input('user');
         }

         Delivery::create($formFields);

         return response()->json($request);
     }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminRequest $request, Order $order)
    {
        try {
            $order->status = $request->input('status');
            $order->save();
        }  catch (\Exception $e) {
            return response()->json(["error"=>"something went wrong"] , 500);
        }

        return response()->json(true) ;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $order->delete();
        }catch (\Exception $e) {
            return response()->json($e , 500);
        }
        return response()->json(true);
    }
}
