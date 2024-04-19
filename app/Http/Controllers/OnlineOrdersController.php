<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrders;
use Illuminate\Http\Request;

class OnlineOrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $OrdersProduct = OnlineOrders::with(["orders" , 'user'])->latest()->get();

        return response()->json($OrdersProduct);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OnlineOrders $onlineOrders)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OnlineOrders $onlineOrders)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OnlineOrders $onlineOrders)
    {
        //
    }
}
