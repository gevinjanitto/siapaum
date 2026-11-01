<?php

namespace App\Http\Controllers\OpenApi;

use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (!auth('open-api')->attempt($request->only('username', 'password'))) {
            return ResponseFormatter::error([
                'message' => 'Valid credential required'
            ], 'Unauthorized', 401);
        }

        $user = auth('open-api')->user();

        $token_expiry = now()->addHours(24);
        $token = $user->createToken('open-api-token', ['*'], $token_expiry)->plainTextToken;

        return ResponseFormatter::success([
            'user' => [
                'username' => $user->username,
                'name' => $user->name,
            ],
            'credential' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_timezone' => 'GMT+8',
                'expires_at' => $token_expiry->setTimezone('GMT+8')->toDateTimeString(),
            ]
        ], 'Login success');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ResponseFormatter::success([], 'Logout success');
    }
}
