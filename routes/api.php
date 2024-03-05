<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('init', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get("/addGuestCartToUser" ,[\App\Http\Controllers\CartController::class , "addGuestCartToUser"]);
    Route::get('/get-cart',[\App\Http\Controllers\CartController::class , "getCart"]);
    Route::get('/getUserCart' ,[\App\Http\Controllers\CartController::class , "UserCart"]) ;
    Route::post('/create-payment-intent', [\App\Http\Controllers\PaymentIntentController::class , 'createPaymentIntent']);
    Route::get('/removeUserCartAfterPayment', [\App\Http\Controllers\CartController::class , 'removeUserCartAfterPayment']);
    Route::get('/GetUserOrders', [\App\Http\Controllers\CartController::class , 'getUserOrders']);
    Route::post('/addProduct' , [\App\Http\Controllers\ProductController::class ,'store']);
    Route::put('/editProduct/{product}' , [\App\Http\Controllers\ProductController::class ,'update']);
    Route::delete('/product/{product}' , [\App\Http\Controllers\ProductController::class ,'destroy']);
}
);
Route::get('/getGuestCart' ,[\App\Http\Controllers\CartController::class , "GuestCart"]) ;
Route::post("/addToCart" ,[\App\Http\Controllers\CartController::class , "addToCart"]);
Route::post("/removeFromCart" ,[\App\Http\Controllers\CartController::class , "removeProductFromCart"]);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('/getProducts' , [\App\Http\Controllers\ProductController::class ,'index']);

