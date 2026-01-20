<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserRequest;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = $request->input('per_page', 15);
            $branchId = $request->input('branch_id');

            $users = $this->userService->getAll($user->organization_id, $branchId, $perPage);

            return $this->paginatedResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve users: '.$e->getMessage());
        }
    }

    public function store(UserRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $currentUser = $request->user();

            if (! $currentUser->isAdmin() && $data['organization_id'] != $currentUser->organization_id) {
                return $this->forbiddenResponse('You can only create users for your organization');
            }

            $user = $this->userService->create($data);

            return $this->createdResponse($user, 'User created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create user: '.$e->getMessage());
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $user = $this->userService->findByIdAndOrganization($id, $currentUser->organization_id);

            if (! $user) {
                return $this->notFoundResponse('User not found');
            }

            $user->load(['organization', 'branch']);

            return $this->successResponse($user, 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve user: '.$e->getMessage());
        }
    }

    public function update(UserRequest $request, int $id): JsonResponse
    {
        try {
            $currentUser = $request->user();
            $user = $this->userService->findByIdAndOrganization($id, $currentUser->organization_id);

            if (! $user) {
                return $this->notFoundResponse('User not found');
            }

            $data = $request->validated();
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $user = $this->userService->update($user, $data);

            return $this->successResponse($user, 'User updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update user: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $currentUser = $request->user();

            if ($currentUser->id === $id) {
                return $this->errorResponse('You cannot delete your own account', 400);
            }

            $user = $this->userService->findByIdAndOrganization($id, $currentUser->organization_id);

            if (! $user) {
                return $this->notFoundResponse('User not found');
            }

            $this->userService->delete($user);

            return $this->successResponse(null, 'User deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete user: '.$e->getMessage());
        }
    }
}
