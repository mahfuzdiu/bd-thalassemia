<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnum;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegistrationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * user registration
     * @param UserRegistrationRequest $request
     * @return JsonResponse
     */
    public function register(UserRegistrationRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);
        return response()->json(['message' => __('messages.user_created')], 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        try{
            $credentials = $request->validated();
            if($jwtToken = JWTAuth::attempt($credentials)){
                return response()->json(['jwt_token' => $jwtToken]);
            }
            return \response()->json(['message' => __('messages.credentials_error')]);

        } catch (JWTException $e){
            return \response()->json(['message' => $e->getMessage()]);
        }

    }

    // 🔹 Logout
    public function logout()
    {
        try{
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => __('messages.logged_out')]);
        }catch (JWTException $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function refreshToken()
    {
        try{
            $token = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);
            return response()->json(['jwt_token' => $newToken, 'expires_in' => JWTAuth::factory()->getTTL() * 60]);

        }catch (\Exception $e){
            return response()->json(['message' => __('messages.token_expiration_error')]);
        }
    }
}
