<?php

namespace App\Services;

use App\Models\Organization;
use Exception;

class OrganizationService
{
    public function index(?string $search = null, int $perPage = 15): array
    {
        try {
            $query = Organization::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            $organizations = $query->orderBy('name')->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Organizations retrieved successfully',
                'data' => $organizations->items(),
                'pagination' => [
                    'current_page' => $organizations->currentPage(),
                    'total_pages' => $organizations->lastPage(),
                    'per_page' => $organizations->perPage(),
                    'total' => $organizations->total(),
                    'next_page_url' => $organizations->nextPageUrl(),
                    'prev_page_url' => $organizations->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve organizations: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data): array
    {
        try {
            $organization = Organization::create($data);

            return [
                'success' => true,
                'message' => 'Organization created successfully',
                'data' => $organization,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create organization: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function show(int $id): array
    {
        try {
            $organization = Organization::find($id);

            if (!$organization) {
                return [
                    'success' => false,
                    'message' => 'Organization not found',
                    'status' => 404,
                ];
            }

            $organization->load(['branches', 'users']);

            return [
                'success' => true,
                'message' => 'Organization retrieved successfully',
                'data' => $organization,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve organization: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $organization = Organization::find($id);

            if (!$organization) {
                return [
                    'success' => false,
                    'message' => 'Organization not found',
                    'status' => 404,
                ];
            }

            $organization->update($data);

            return [
                'success' => true,
                'message' => 'Organization updated successfully',
                'data' => $organization->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update organization: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id): array
    {
        try {
            $organization = Organization::find($id);

            if (!$organization) {
                return [
                    'success' => false,
                    'message' => 'Organization not found',
                    'status' => 404,
                ];
            }

            $organization->delete();

            return [
                'success' => true,
                'message' => 'Organization deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete organization: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
