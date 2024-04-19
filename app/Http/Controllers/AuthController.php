<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
       try{
           $data = $request->validate([
                   'email' => ['bail', 'required', 'email', 'min:3', 'max:50', 'unique:users'],
                   'password' => ['bail', 'required', 'string', 'min:6'],
                   'address' => ['bail', 'required', 'string', 'min:10'],
                   'firstName' => ['bail', 'required', 'string', 'min:3'],
                   'lastName' => ['bail', 'required', 'string', 'min:3'],
                   'phone' => ['bail', 'required', 'string', 'min:6'],
                   'city' => ['bail', 'required', 'string', 'min:6'],
               ]
           );
       }catch (\Illuminate\Validation\ValidationException $e) {
           // Optimized error response handling for validation errors:
           $errors = $e->errors();

           // Option 1: Return all errors as an array (recommended)
           return response()->json(["errors"=>$errors], 422);
//           return response()->json([array_key_first($errors)=>$errors[array_key_first($errors)]], 422);

           // Option 2: Return the first error only
           // return response()->json(['error' => $errors[array_key_first($errors)], 422);
       }

        DB::beginTransaction();

       $user =  User::create($data);

        DB::commit();

       if($user)
       {
           return response()->json(true );
       }
       return response()->json(false);
    }

    public function login(Request $request)
    {
        $data = [
            'email'    => $request->email,
            'password' => $request->password,
        ];

        if (! auth()->attempt($data)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $tokenName = config('APP_NAME', 'TOKEN_NAME');
        $expiresAt = app()->isLocal() ? now()->addYear() : now()->addDay();
        $token     = $request->user()->createToken($tokenName, ['*'], $expiresAt);

        return response()->json(['token' => $token->plainTextToken]);
    }

    public function logout()
    {
        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json(true);
    }

    public function user()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $user->makeHidden(['created_at', 'updated_at' ,'deleted_at']);

        return response()->json($user);
    }
}
