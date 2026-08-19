<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Response\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $auth_service) {}

    public function register(RegisterRequest $request)
    {
        $result = $this->auth_service->register($request->validated());

        if ($result) {
            return ApiResponse::error(null, 'please follow the rule', 401);
        }

        return ApiResponse::success($result, 'register success', 200);

    }

    public function login(LoginRequest $request)
    {
        $result = $this->auth_service->login($request->validated());

        if (! $result) {
            return ApiResponse::error(null, 'credential salah, tolong dicek sekali lagi', 401);
        }

        return ApiResponse::success($result, 'login success', 200);
    }

    public function logout(Request $request)
    {
        $this->auth_service->logout($request->user());

        return ApiResponse::success(null, 'logout success', 200);
    }
}
