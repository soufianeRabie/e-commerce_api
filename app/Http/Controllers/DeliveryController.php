<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Guest_basket;
use App\Models\Order;
use App\Models\Product;
use App\Models\TmepOrder;
use App\Models\User_basket;
use Illuminate\Http\Request;
use App\Http\Controllers;
use Psy\Util\Str;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $DeliveriesProduct = Delivery::with("orders")->latest()->get();

        return response()->json($DeliveriesProduct);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        try {
            $userIpAddress = $request->getClientIp();
            $userId = $request->input("user", null);
            if (!$userId) {

                $Basket = Guest_basket::where('ip_address', $userIpAddress)->get();

            } else {
                $Basket = User_basket::where('user_id', $userId)->get();
            }

            $totalAmount = CartController::totalAmount($Basket);

            $basketLength = $Basket->count();

            $totalDiscount = $basketLength > 14 ? 7 : $basketLength * 0.5 ;

            $user = Delivery::create([
                "email" => $request->input("email"),
                "address" => $request->input("address"),
                "phone" => $request->input("phone"),
                "first_name" => $request->input("firstName"),
                "last_name" => $request->input("lastName"),
                "TotalDiscount" =>$totalDiscount,
                "amount" => $totalAmount,
                "city" => $request->input("city"),
                "userId" => $userId,
                "status" => "pending",
                "ip_address" => $userIpAddress
            ]);
            foreach ($Basket as $item) {
                $counter = 0 ;
                $counter += 0.5;
                $product = Product::where('id' ,$item->product_id)->first();
                $discount = $counter > 14 ?0 : 0.5;
                Order::create([
                    "email" => $request->input("email"),
                    "product_id" => $item->product_id,
                    "ip_address" => $userIpAddress,
                    "quantity" => $item->quantity,
                    "price"=>$product->price,
                    "totalPrice"=>$item->quantity * $product->price,
                    "user_id" => $userId,
                    "discount"=>$discount,
                    "payment_method" => "cash",
                    "deliveriesId" =>$user->id
                ]);
                $item->delete();
            }

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
        return response()->json([$Basket] );
    }

    /**
     * Display the specified resource.
     */
    public function show(Delivery $delivery)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Delivery $delivery)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Delivery $delivery)
    {

//        return response()->json(['status'=>$request->input('status') , $delivery]);
        try {
            $OrdersForThisDelivery = Order::where('deliveriesId' , $delivery->id)->get();
            foreach ($OrdersForThisDelivery as $Order)
            {
                $Order->status = $request->input('status');
                $Order->save();
            }
            $delivery->status = $request->input('status');
            $delivery->save();
        }catch (\Exception $e)
        {
            return response()->json($e);
        }
        return response()->json(true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Delivery $delivery)
    {
        try {
            $OrdersForThisDelivery = Order::where('deliveriesId' , $delivery->id)->get();
            foreach ($OrdersForThisDelivery as $Order)
            {
                $Order->delete();
            }
            $delivery->delete();
        }catch (\Exception $e) {
            return response()->json($e , 500);
        }
        return response()->json(true);
    }


    public static  function getTotalAmount($Basket)
    {
        $totalAmount = 0 ;
        $discount = 0 ;
        foreach ($Basket as $item)
        {
            $product = Product::where('id', $item->product_id)->first();
//                return response()->json([$product->price]);
            $amount = $discount <= 7 ?
                 PaymentIntentController::AmountAfterDiscount($product->price * $item->quantity, 0.5)
                : $product->price * $item->quantity;
            $discount += 0.5;
            $totalAmount += $amount;
        }

        return $totalAmount;
    }
}
