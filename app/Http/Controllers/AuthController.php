<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        /**
         * ==========1===========
         * Validasi data registrasi yang masuk
         */
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        /**
         * =========2===========
         * Buat user baru dan generate token API, atur masa berlaku token 1 jam
         */
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate token Passport
        $tokenResult = $user->createToken('API Token');

        // Token expired dalam 1 jam
        $tokenResult->token->expires_at = now()->addHour();
        $tokenResult->token->save();

        $token = $tokenResult->accessToken;

        /**
         * =========3===========
         * Kembalikan response sukses dengan data user dan token
         */
        return response()->json([
            'status'  => true,
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
            'expires_in' => 3600
        ], 201);
    }


    public function login(Request $request)
    {
        /**
         * =========4===========
         * Validasi data login yang masuk
         */
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Periksa kredensial
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        $user = Auth::user();

        /**
         * =========5===========
         * Generate token API untuk user
         * Atur token expired dalam 1 jam
         */
        $tokenResult = $user->createToken('API Token');

        $tokenResult->token->expires_at = now()->addHour();
        $tokenResult->token->save();

        $token = $tokenResult->accessToken;

        /**
         * =========6===========
         * Kembalikan response sukses
         */
        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'user'    => $user,
            'token'   => $token,
            'expires_in' => 3600
        ], 200);
    }


    public function logout(Request $request)
    {
        /**
         * =========7===========
         * Invalidate token yang digunakan saat ini
         */
        $request->user()->token()->revoke();

        /**
         * =========8===========
         * Kembalikan response sukses
         */
        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully'
        ], 200);
    }
}
