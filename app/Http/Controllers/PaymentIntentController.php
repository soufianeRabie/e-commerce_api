<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User_basket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentIntentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {

        $user = Auth::user();
        if($user)
        {
            Stripe::setApiKey(config('services.stripe.secret'));

            $amount  = (float)number_format($this->getAmountOfTheProduct($user), 2, '.', '') * 100;
            $currency = 'usd'; // Adjust as needed

            try {
                $paymentIntent = PaymentIntent::create([
                    'amount' => $amount ,
                    'currency' => $currency,
                ]);

                return response()->json(['clientSecret' => $paymentIntent->client_secret , $amount]);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage() , $amount], 500);
            }
        }else
        {
            return response()->json("error get client secret" , 500);
        }
    }

    private function getAmountOfTheProduct($user)
    {
        $discount = 0 ;
        $discountForEachProduct = 0.5 ;
        $TotalPrice = 0;
        $productsWithQuantities = DB::table('user_basket')
            ->join('products', 'user_basket.product_id', '=', 'products.id')
            ->where('user_basket.user_id', '=', $user->id)
            ->select('products.price', 'user_basket.quantity')
            ->get();
        foreach ($productsWithQuantities as $product) {
            // Access product details, e.g., $product->name, $product->price, etc.
            $TotalPrice += $product->price * $product->quantity;
            $discount  += $discountForEachProduct;
        }

        return $this->AmountAfterDiscount($TotalPrice , $discount);
    }

    private function AmountAfterDiscount($totalPrice , $discount)
    {
        $maxDiscount = 7;
        if($discount > 0 && $discount < $maxDiscount )
        {
            return $totalPrice -( $totalPrice * ($discount / 100));
        }

        if($discount < 0 && $discount >= $maxDiscount)
        {
            return ($totalPrice - ($totalPrice * ($maxDiscount / 100)));
        }

        return $totalPrice;
    }
}
