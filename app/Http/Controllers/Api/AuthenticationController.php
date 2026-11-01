<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();

            // handle jika user tidak ditemukan
            if (!$user) {
                return ResponseFormatter::error([], 'Unauthorized', 401);
            }

            // handle jika password salah
            if (!Hash::check($request->password, $user->password)) {
                return ResponseFormatter::error([], 'Unauthorized', 401);
            }

            // handle role user
            if ($user->role_id != RoleEnum::INPUTER->value) {
                return ResponseFormatter::error([], 'Unauthorized', 401);
            }

            // delete all token
            $user->tokens()->delete();

            // create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return ResponseFormatter::success([
                'user' => new UserResource($user),
                'credential' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer'
                ]
            ], 'Login success');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => 'Internal Server Error',
            ], 'Login failed', 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return ResponseFormatter::success([], 'Logout success');
        } catch (\Exception $e) {
            Log::error($e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return ResponseFormatter::error([
                'message' => 'Internal Server Error',
            ], 'Logout failed', 500);
        }
    }
}
