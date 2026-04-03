<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Login Logic
    public function handleLogin(Request $request){
        $request->validate([
            "email" => "required|string|email|min:2|max:255",
            "password" => "required|string|min:8|max:255",
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'Invalid Login details, please check and retry again'
            ], 422);
        } 
        else{
            $token = $user->createToken($user->firstName . 'Auth-Token')->plainTextToken;

            return response()->json([
                'message' => 'Login Successful',
                'token_Type' => 'Bearer',
                'token' => $token
            ], 200);
        }
    }

    public function handleRegister(Request $request){
        // validate input fields
            $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed'
            ]);
            

            $user = User::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' =>$request->email,
                'password' => Hash::make($request->password)
            ]);

            // create a Auth token for user
            $token = $user->createToken($user->firstName . 'Auth-Token')->plainTextToken;

            return response()->json([
                'message' => 'User Registered Successfully',
                'token_Type' => 'Bearer',
                'token' => $token,
                'data' => $user
            ], 201);

    }
}
