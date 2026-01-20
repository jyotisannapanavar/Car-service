<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->attemptLogin(
                $request->email,
                $request->password
            );

            if (! $user) {
                return $this->unauthorizedResponse('Invalid credentials or account is inactive');
            }

            $token = $this->authService->createToken(
                $user,
                $request->device_name ?? 'api'
            );

            return $this->successResponse([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'organization_id' => $user->organization_id,
                    'branch_id' => $user->branch_id,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Login failed: '.$e->getMessage());
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->revokeCurrentToken($request->user());

            return $this->successResponse(null, 'Logged out successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Logout failed: '.$e->getMessage());
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->load(['organization', 'branch']);

            return $this->successResponse([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'organization' => $user->organization,
                'branch' => $user->branch,
            ], 'User profile retrieved');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve profile: '.$e->getMessage());
        }
    }
}
