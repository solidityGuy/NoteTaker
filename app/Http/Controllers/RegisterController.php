<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{

    public function register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            $token = $user->createToken('authToken')->plainTextToken;
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        $cookie = cookie('auth_token', $token, 60 * 24 * 7, null, null, false, true);
        return response()->json(['user' => $user, 'token' => $token], 201)->withCookie($cookie);
    }
}
