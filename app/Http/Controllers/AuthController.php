<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Response\ApiResponse;
use App\Services\AuthService;
use App\Swagger\ApiSuccessDoc;
use App\Swagger\JsonSchemaRef;
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
            new ApiSuccessDoc(201, 'register success', [
                new OA\Property(property: 'name', type: 'string', example: 'Joko Widodo'),
                new OA\Property(property: 'email', type: 'string', example: 'jokowi@gmail.com'),
                new OA\Property(property: 'nip_nik', type: 'string', example: '199001012020121004'),
                new OA\Property(property: 'phone', type: 'string', example: '081234567895'),
            ]),
        ]
    )]
    public function register(RegisterRequest $request)
    {
        $result = $this->auth_service->register($request->validated());

        return ApiResponse::success($result, 'register success', 201);
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
            new ApiSuccessDoc(200, 'login success', [
                new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'string'), example: ['create-request', 'view-request']),
                new OA\Property(property: 'token', type: 'string', example: '6|3Kgs2B34mqxB22hmHc9W8N2GYXxwg3OTtWT4I4A84756196e'),
            ]),
        ]
    )]
    public function login(LoginRequest $request)
    {
        $result = $this->auth_service->login($request->validated());

        if (! $result) {
            return ApiResponse::error(null, 'login failed, please check again credential', 400);
        }

        return ApiResponse::success($result, 'login success', 200);
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
