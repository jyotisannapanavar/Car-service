<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data): array
    {
        try {
            // Check if email already exists
            $existingUser = User::where('email', $data['email'])->first();
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Email already registered',
                    'status' => 422,
                ];
            }

            // Create new user
            $user = User::create([
                'org_id' => $data['org_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Create token for auto-login after registration
            $token = $user->createToken('api')->plainTextToken;

            return [
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'org_id' => $user->org_id,
                        'branch_id' => $user->branch_id,
                        'is_active' => $user->is_active,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Registration failed: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function login(string $email, string $password, string $deviceName = 'api'): array
    {
        try {
            $user = User::where('email', $email)->first();

            if (! $user || ! Hash::check($password, $user->password)) {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials or account is inactive',
                    'status' => 401,
                ];
            }

            if (! $user->is_active) {
                return [
                    'success' => false,
                    'message' => 'Invalid credentials or account is inactive',
                    'status' => 401,
                ];
            }

            $token = $user->createToken($deviceName)->plainTextToken;

            return [
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'org_id' => $user->org_id,
                        'branch_id' => $user->branch_id,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Login failed: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function logout(User $user): array
    {
        try {
            $user->currentAccessToken()->delete();

            return [
                'success' => true,
                'message' => 'Logged out successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Logout failed: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function getProfile(User $user): array
    {
        try {
            $user->load(['organization', 'branch']);

            return [
                'success' => true,
                'message' => 'User profile retrieved',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'is_active' => $user->is_active,
                    'organization' => $user->organization,
                    'branch' => $user->branch,
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve profile: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
