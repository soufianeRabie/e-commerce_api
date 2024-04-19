<?php

namespace App\Http\Controllers;

use App\Models\TmepOrder;
use Illuminate\Http\Request;


class TmepOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $CashDeliveryOrders = TmepOrder::all();
        return response()->json($CashDeliveryOrders);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(tmepOrder $tmepOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tmepOrder $tmepOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, tmepOrder $tmepOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(tmepOrder $tmepOrder)
    {
        //
    }
}
