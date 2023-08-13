<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::where('email', $validatedData['email'])->first();

            if (!$user || !Hash::check($validatedData['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials'],
                ]);
            }

            $token = $user->createToken('authToken')->plainTextToken;
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        $cookie = cookie('auth_token', $token, 60 * 24 * 7, null, null, false, true);
        return response()->json(['user' => $user, 'token' => $token], 201)->withCookie($cookie);
    }
}
