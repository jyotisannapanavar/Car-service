<?php

namespace App\Services;

use App\Models\User;
use App\Models\VehicleType;
use App\Traits\TenantScope;
use Exception;

class VehicleTypeService
{
    use TenantScope;

    public function index(User $user, ?string $search = null, int $perPage = 15): array
    {
        try {
            $query = $this->applyTenantScope(VehicleType::query(), $user);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $vehicleTypes = $query->orderBy('name')->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Vehicle types retrieved successfully',
                'data' => $vehicleTypes->items(),
                'pagination' => [
                    'current_page' => $vehicleTypes->currentPage(),
                    'total_pages' => $vehicleTypes->lastPage(),
                    'per_page' => $vehicleTypes->perPage(),
                    'total' => $vehicleTypes->total(),
                    'next_page_url' => $vehicleTypes->nextPageUrl(),
                    'prev_page_url' => $vehicleTypes->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle types: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function list(User $user): array
    {
        try {
            $query = $this->applyTenantScope(VehicleType::where('is_active', true), $user);
            $vehicleTypes = $query->orderBy('name')->get();

            return [
                'success' => true,
                'message' => 'Vehicle types retrieved successfully',
                'data' => $vehicleTypes,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle types: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data): array
    {
        try {
            $vehicleType = VehicleType::create($data);

            return [
                'success' => true,
                'message' => 'Vehicle type created successfully',
                'data' => $vehicleType,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create vehicle type: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function show(int $id, User $user): array
    {
        try {
            $vehicleType = $this->applyTenantScope(VehicleType::where('id', $id), $user)->first();

            if (!$vehicleType) {
                return [
                    'success' => false,
                    'message' => 'Vehicle type not found',
                    'status' => 404,
                ];
            }

            $vehicleType->load('vehicleBrands');

            return [
                'success' => true,
                'message' => 'Vehicle type retrieved successfully',
                'data' => $vehicleType,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle type: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, User $user, array $data): array
    {
        try {
            $vehicleType = $this->applyTenantScope(VehicleType::where('id', $id), $user)->first();

            if (!$vehicleType) {
                return [
                    'success' => false,
                    'message' => 'Vehicle type not found',
                    'status' => 404,
                ];
            }

            $vehicleType->update($data);

            return [
                'success' => true,
                'message' => 'Vehicle type updated successfully',
                'data' => $vehicleType->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update vehicle type: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id, User $user): array
    {
        try {
            $vehicleType = $this->applyTenantScope(VehicleType::where('id', $id), $user)->first();

            if (!$vehicleType) {
                return [
                    'success' => false,
                    'message' => 'Vehicle type not found',
                    'status' => 404,
                ];
            }

            $vehicleType->delete();

            return [
                'success' => true,
                'message' => 'Vehicle type deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete vehicle type: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
