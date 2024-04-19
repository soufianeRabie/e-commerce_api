<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Models\complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $complaints = complaint::with('user')->get();

        return response()->json($complaints);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getUserComplaints()
    {
        try {
            $user = Auth::user();

            $complaints = complaint::where('user_id', $user->id);
        }catch (\Exception $e) {
            return response()->json($e , 500);
        }

        return response()->json($complaints);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $addressIp = $request->getClientIp();
        $formFields = $request->post();
        $formFields['address_ip'] = $addressIp;

        try {
          complaint::create($formFields);
        }catch (\Exception $e) {
            return response()->json($e , 500);
        }

        return true ;
    }

    /**
     * Display the specified resource.
     */
    public function show(complaint $complaint)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(complaint $complaint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, complaint $complaint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdminRequest $request , complaint $complaint)
    {
        try {
            $complaint->delete();
        }catch (\Exception $e)
        {
            return response()->json($e);
        }

        return response()->json(true);


    }
}
