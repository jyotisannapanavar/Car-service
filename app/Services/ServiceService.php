<?php

namespace App\Services;

use App\Models\Service;
use App\Models\User;
use App\Traits\TenantScope;
use Exception;

class ServiceService
{
    use TenantScope;

    public function index(User $user, ?string $search = null, int $perPage = 15): array
    {
        try {
            $query = $this->applyTenantScope(Service::query(), $user);


            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $services = $query->orderBy('name')->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Services retrieved successfully',
                'data' => $services->items(),
                'pagination' => [
                    'current_page' => $services->currentPage(),
                    'total_pages' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                    'next_page_url' => $services->nextPageUrl(),
                    'prev_page_url' => $services->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve services: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function listByBranch(int $branchId): array
    {
        try {
            $services = Service::where('branch_id', $branchId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return [
                'success' => true,
                'message' => 'Services retrieved successfully',
                'data' => $services,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve services: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data): array
    {
        try {
            $service = Service::create($data);

            return [
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create service: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function show(int $id, User $user): array
    {
        try {
            $service = $this->applyTenantScope(Service::where('id', $id), $user)->first();

            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Service not found',
                    'status' => 404,
                ];
            }

            $service->load(['organization', 'branch', 'vehicleServicePricing']);

            return [
                'success' => true,
                'message' => 'Service retrieved successfully',
                'data' => $service,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve service: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, User $user, array $data): array
    {
        try {
            $service = $this->applyTenantScope(Service::where('id', $id), $user)->first();

            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Service not found',
                    'status' => 404,
                ];
            }

            $service->update($data);

            return [
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update service: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id, User $user): array
    {
        try {
            $service = $this->applyTenantScope(Service::where('id', $id), $user)->first();

            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'Service not found',
                    'status' => 404,
                ];
            }

            $service->delete();

            return [
                'success' => true,
                'message' => 'Service deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete service: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
