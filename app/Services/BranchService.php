<?php

namespace App\Services;

use App\Models\Branch;
use Exception;

class BranchService
{
    public function index(int $orgId, int $perPage = 15): array
    {
        try {
            $branches = Branch::where('org_id', $orgId)
                ->orderBy('name')
                ->paginate($perPage);

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
                'message' => 'Failed to retrieve branches: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data, int $userOrgId): array
    {
        try {
            if ($data['org_id'] != $userOrgId) {
                return [
                    'success' => false,
                    'message' => 'You can only create branches for your organization',
                    'status' => 403,
                ];
            }

            $branch = Branch::create($data);

            return [
                'success' => true,
                'message' => 'Branch created successfully',
                'data' => $branch,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create branch: '.$e->getMessage(),
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

            if (! $branch) {
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
                'message' => 'Failed to retrieve branch: '.$e->getMessage(),
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

            if (! $branch) {
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
                'message' => 'Failed to update branch: '.$e->getMessage(),
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

            if (! $branch) {
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
                'message' => 'Failed to delete branch: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
