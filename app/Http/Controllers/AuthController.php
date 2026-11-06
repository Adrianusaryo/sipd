<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\RegisterResource;
use App\Http\Response\ApiResponse;
use App\Services\AuthService;
use App\Swagger\ApiErrorDoc;
use App\Swagger\ApiSuccessDoc;
use App\Swagger\JsonSchemaRef;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(protected AuthService $auth_service) {}

    // Register
    #[OA\Post(
        path: '/auth/register',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonSchemaRef('RegisterRequest')
        ),
        responses: [
            new ApiSuccessDoc(201, 'register success', new JsonSchemaRef('RegisterResource')),
        ]
    )]
    public function register(RegisterRequest $request)
    {
        $result = $this->auth_service->register($request->validated());

        return ApiResponse::success(new RegisterResource($result), 'register success', 201);
    }

    // Login
    #[OA\Post(
        path: '/auth/login',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonSchemaRef('LoginRequest')
        ),
        responses: [
            new ApiSuccessDoc(200, 'login success',new JsonSchemaRef('LoginResource')),
            new ApiErrorDoc(400, 'login failed, please check your credential'),
        ]
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth_service->login($request->validated());

        if (! $result) {
            return ApiResponse::error(null, 'login failed, please check again credential', 401);
        }

        return ApiResponse::success(new LoginResource($result), 'login success', 200);
    }

    // Logout
    #[OA\Post(
        path: '/auth/logout',
        tags: ['Authentication'],
        security: [['bearerAuth' => []]],
        responses: [
            new ApiSuccessDoc(200, 'logout success'),
        ]
    )]
    public function logout(Request $request)
    {
        $this->auth_service->logout($request->user());

        return ApiResponse::success(null, 'logout success', 200);
    }
}
