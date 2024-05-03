<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function UpdateRole(Request $request , $id){
        $user=User::find($id);
        $value=$request->post();
        $user->fill($value)->save();
        return response()->json(true);
    }
    public function Destroy($id){
        $user=User::find($id);
        $user->delete();
        return response()->json(true);
    }
}