<?php

namespace App\Services;

use App\Models\User;
use App\Traits\TenantScope;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserService
{
    use TenantScope;

    public function index(User $authUser, ?string $search = null, int $perPage = 15): array
    {
        try {
            $query = $this->applyTenantScope(User::query(), $authUser);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $users = $query->orderBy('name')->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Users retrieved successfully',
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'total_pages' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'next_page_url' => $users->nextPageUrl(),
                    'prev_page_url' => $users->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve users: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data): array
    {
        try {
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);

            return [
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create user: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function show(int $id, User $authUser): array
    {
        try {
            $user = $this->applyTenantScope(User::where('id', $id), $authUser)->first();

            if (! $user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'status' => 404,
                ];
            }

            $user->load(['organization', 'branch']);

            return [
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => $user,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve user: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, User $authUser, array $data): array
    {
        try {
            $user = $this->applyTenantScope(User::where('id', $id), $authUser)->first();

            if (! $user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'status' => 404,
                ];
            }

            if (isset($data['password']) && ! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return [
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update user: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id, User $authUser): array
    {
        try {
            if ($authUser->id === $id) {
                return [
                    'success' => false,
                    'message' => 'You cannot delete your own account',
                    'status' => 400,
                ];
            }

            $user = $this->applyTenantScope(User::where('id', $id), $authUser)->first();

            if (! $user) {
                return [
                    'success' => false,
                    'message' => 'User not found',
                    'status' => 404,
                ];
            }

            $user->delete();

            return [
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete user: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
