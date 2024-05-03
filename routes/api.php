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


Route::middleware(['auth:sanctum','isAdmin'])->group(function () {
    Route::get('init', [AuthController::class, 'user'])->withoutMiddleware('isAdmin');
    Route::post('logout', [AuthController::class, 'logout'])->withoutMiddleware('isAdmin');
    Route::get("/addGuestCartToUser" ,[\App\Http\Controllers\CartController::class , "addGuestCartToUser"])->withoutMiddleware('isAdmin');
    Route::get('/get-cart',[\App\Http\Controllers\CartController::class , "getCart"])->withoutMiddleware('isAdmin');
    Route::get('/getUserCart' ,[\App\Http\Controllers\CartController::class , "UserCart"])->withoutMiddleware('isAdmin') ;
    Route::post('/create-payment-intent', [\App\Http\Controllers\PaymentIntentController::class , 'createPaymentIntent'])->withoutMiddleware('isAdmin');
    Route::get('/removeUserCartAfterPayment', [\App\Http\Controllers\CartController::class , 'removeUserCartAfterPayment'])->withoutMiddleware('isAdmin');
    Route::get('/GetUserOrders', [\App\Http\Controllers\CartController::class , 'getUserOrders'])->withoutMiddleware('isAdmin');
    Route::post('/addProduct' , [\App\Http\Controllers\ProductController::class ,'store']);
    Route::put('/editProduct/{product}' , [\App\Http\Controllers\ProductController::class ,'update']);
    Route::delete('/product/{product}' , [\App\Http\Controllers\ProductController::class ,'destroy']);
    Route::get('/admin/orders' , [\App\Http\Controllers\OrderController::class ,'index']);
    Route::get('/admin/deliveries' , [\App\Http\Controllers\DeliveryController::class ,'index']);
    Route::get('/admin/orders_online' , [\App\Http\Controllers\OnlineOrdersController::class ,'index']);
    Route::put('/admin/deliveries/{delivery}' , [\App\Http\Controllers\DeliveryController::class ,'update']);
    Route::delete('/admin/deliveries/{delivery}' , [\App\Http\Controllers\DeliveryController::class ,'destroy']);
    Route::put('/admin/orders/{order}' , [\App\Http\Controllers\OrderController::class ,'update']);
    Route::delete('/admin/orders/{order}' , [\App\Http\Controllers\OrderController::class ,'destroy']);
    Route::delete('/admin/complaints/{complaint}' , [\App\Http\Controllers\ComplaintController::class ,'destroy']);
    Route::get('/admin/CountUsers' , [\App\Http\Controllers\Dashboard::class ,'GetUserSubscriptionForTwoMonth']);
    Route::get('/admin/TotalRevenue' , [\App\Http\Controllers\Dashboard::class , 'CalculateTotalRevenue']);
//    Route::get('/admin/CountUsers' , [\App\Http\Controllers\Dashboard::class ,'GetUserSubscriptionForTwoMonth']);
}
);
Route::get('/getGuestCart' ,[\App\Http\Controllers\CartController::class , "GuestCart"]) ;
Route::post("/addToCart" ,[\App\Http\Controllers\CartController::class , "addToCart"]);
Route::post("/removeFromCart" ,[\App\Http\Controllers\CartController::class , "removeProductFromCart"]);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/delivery', [\App\Http\Controllers\DeliveryController::class, 'store']);
Route::get('/getProducts' , [\App\Http\Controllers\ProductController::class ,'index']);
Route::get('/complaints', [\App\Http\Controllers\ComplaintController::class , 'index']);
Route::get('/user/complaints', [\App\Http\Controllers\ComplaintController::class , 'getUserComplaints']);
Route::post('/complaints', [\App\Http\Controllers\ComplaintController::class , 'store']);

