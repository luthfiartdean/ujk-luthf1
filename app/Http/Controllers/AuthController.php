<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:app_users',
            'password' => 'required|min:6',
            'phone' => 'nullable|string',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json([
                'message' => 'Register berhasil',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Register gagal',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $credentials = $request->only('email', 'password');

            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Email atau password salah',
                ], 401);
            }

            return response()->json([
                'message' => 'Login berhasil',
                'user' => auth()->user(),
                'token' => $token,
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Login gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile()
    {
        return response()->json([
            'user' => auth()->user(),
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'message' => 'Token berhasil di-refresh',
            'token' => $token,
        ]);
    }
}