<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class BranchService
{
    public function index(int $orgId, ?string $search = null, int $perPage = 15): array
    {
        try {
            $query = Branch::where('org_id', $orgId);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $branches = $query->latest()->paginate($perPage);


            return [
                'success' => true,
                'message' => 'Branches retrieved successfully',
                'data' => $branches->items(),
                'pagination' => [
                    'current_page' => $branches->currentPage(),
                    'total_pages' => $branches->lastPage(),
                    'per_page' => $branches->perPage(),
                    'total' => $branches->total(),
                    'next_page_url' => $branches->nextPageUrl(),
                    'prev_page_url' => $branches->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve branches: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }



    public function store(array $data, $authUser): array
    {
        DB::beginTransaction();

        try {
            /**
             * 1️⃣ Create Branch (org_id from logged-in user or provided)
             */
            $orgId = $data['branch']['org_id'] ?? $authUser->org_id;

            $branch = Branch::create([
                'org_id' => $orgId,
                'name' => $data['branch']['name'],
                'code' => $data['branch']['code'] ?? null,
                'email' => $data['branch']['email'] ?? null,
                'phone' => $data['branch']['phone'] ?? null,
                'address' => $data['branch']['address'] ?? null,
                'is_active' => $data['branch']['is_active'] ?? true,
            ]);

            /**
             * 2️⃣ Create Branch Admin User
             */
            $user = User::create([
                'name' => $data['user']['name'],
                'email' => $data['user']['email'] ?? null,
                'phone' => $data['user']['phone'] ?? null,
                'org_id' => $orgId,
                'branch_id' => $branch->id,
                'password' => Hash::make($data['user']['password']),
                'role' => 'branch_admin',
                'is_active' => true,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Branch and branch admin created successfully',
                'data' => [
                    'branch' => $branch,
                    'user' => $user,
                ],
                'status' => 201,
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }



    public function show(int $id, int $orgId): array
    {
        try {
            $branch = Branch::where('id', $id)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Branch not found',
                    'status' => 404,
                ];
            }

            $branch->load(['organization', 'users', 'services']);

            return [
                'success' => true,
                'message' => 'Branch retrieved successfully',
                'data' => $branch,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve branch: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, int $orgId, array $data): array
    {
        try {
            $branch = Branch::where('id', $id)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Branch not found',
                    'status' => 404,
                ];
            }

            $branch->update($data);

            return [
                'success' => true,
                'message' => 'Branch updated successfully',
                'data' => $branch->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update branch: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id, int $orgId): array
    {
        try {
            $branch = Branch::where('id', $id)
                ->where('org_id', $orgId)
                ->first();

            if (!$branch) {
                return [
                    'success' => false,
                    'message' => 'Branch not found',
                    'status' => 404,
                ];
            }

            $branch->delete();

            return [
                'success' => true,
                'message' => 'Branch deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete branch: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
