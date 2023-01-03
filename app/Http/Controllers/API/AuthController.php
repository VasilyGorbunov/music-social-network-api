<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    try {
      $user = User::create([
          'first_name' => $request->input('first_name'),
          'last_name' => $request->input('last_name'),
          'email' => $request->input('email'),
          'password' => Hash::make($request->input('password')),
      ]);

      $token = $user->createToken('user_token')->plainTextToken;

      return response()->json([
          'user' => $user,
          'token' => $token
      ], 200);
    } catch (\Exception $ex) {
      return response()->json([
          'error' => $ex->getMessage(),
          'message' => 'Something went wrong in AuthController.register'
      ]);
    }

  }

  public function login(Request $request)
  {
    try {
      $user = User::where('email', '=', $request->input('email'))->firstOrFail();

      if(Hash::check($request->input('password'), $user->password))
      {
        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);
      }

      return response()->json([
          'error' => 'Something went wrong in AuthController.login'
      ]);

    } catch (\Exception $ex) {
      return response()->json([
          'error' => $ex->getMessage(),
          'message' => 'Something went wrong in AuthController.login'
      ]);
    }

  }

  public function logout(Request $request)
  {
    try {
      $user = User::findOrFail($request->input('user_id'));

      $user->tokens()->delete();

      return response()->json('User logged out', 200);
    } catch (\Exception $ex) {
      return response()->json([
          'error' => $ex->getMessage(),
          'message' => 'Something went wrong in AuthController.logout'
      ]);
    }

  }
}
