<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function login(Request $request)
   {
         $request->validate([
           'email' => 'required|email',
           'password' => 'required|string|min:6',
       ]);
       $credentials = $request->only('email', 'password');

         if (Auth::attempt($credentials)) {
             /** @var \App\Models\User $user **/
              $user = Auth::user();
              if (!$user->email_verified_at) {
                  return response()->json([
                      'message' => 'Email not verified',
                  ], 403);
              }
              $token = $user->createToken("auth_token")->plainTextToken;
              
              return response()->json([
                'message' => 'Login successful',
                'access_token' => $token,
                'user' => $user,
              ]);
         }else {
           return response()->json([
               'message' => 'Invalid credentials',
           ], 401);
       }
   }

   public function logout(Request $request)
   {
       $user = $request->user();
       $user->currentAccessToken()->delete();

       return response()->json([
           'message' => 'Logout successful',
       ]);
   }

   public function signup(Request $request)
   {
       $validated = $request->validate([
           'fname' => 'required|string|max:255',
           'lname' => 'required|string|max:255',
           'username' => 'required|string|max:255|unique:users',
           'email' => 'required|string|email|max:255|unique:users',
           'password' => 'required|string|min:6',
       ]);

        $user = \App\Models\User::create([
            'fname' => $validated['fname'],
            'lname' => $validated['lname'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->save();

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
   }
}
