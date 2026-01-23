<?php

namespace App\Services;

use App\Models\User;
use App\Models\VehicleBrand;
use App\Models\VehicleModel;
use App\Traits\TenantScope;
use Exception;

class VehicleModelService
{
    use TenantScope;

    public function index(User $user, ?int $vehicleBrandId = null, int $perPage = 15): array
    {
        try {
            $query = $this->applyTenantScope(VehicleModel::query(), $user);

            if ($vehicleBrandId) {
                $query->where('vehicle_brand_id', $vehicleBrandId);
            }

            $vehicleModels = $query->orderBy('name')->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Vehicle models retrieved successfully',
                'data' => $vehicleModels->items(),
                'pagination' => [
                    'current_page' => $vehicleModels->currentPage(),
                    'total_pages' => $vehicleModels->lastPage(),
                    'per_page' => $vehicleModels->perPage(),
                    'total' => $vehicleModels->total(),
                    'next_page_url' => $vehicleModels->nextPageUrl(),
                    'prev_page_url' => $vehicleModels->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle models: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function listByBrand(User $user, int $vehicleBrandId): array
    {
        try {
            $query = $this->applyTenantScope(
                VehicleModel::where('vehicle_brand_id', $vehicleBrandId)->where('is_active', true),
                $user
            );
            $vehicleModels = $query->orderBy('name')->get();

            return [
                'success' => true,
                'message' => 'Vehicle models retrieved successfully',
                'data' => $vehicleModels,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle models: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data, User $user): array
    {
        try {
            $data['org_id'] = $user->org_id;
            $data['branch_id'] = $user->branch_id;

            $brand = $this->applyTenantScope(
                VehicleBrand::where('id', $data['vehicle_brand_id']),
                $user
            )->first();

            if (! $brand) {
                return [
                    'success' => false,
                    'message' => 'Vehicle brand does not belong to your organization',
                    'status' => 403,
                ];
            }

            $vehicleModel = VehicleModel::create($data);

            return [
                'success' => true,
                'message' => 'Vehicle model created successfully',
                'data' => $vehicleModel,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create vehicle model: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function show(int $id, User $user): array
    {
        try {
            $vehicleModel = $this->applyTenantScope(VehicleModel::where('id', $id), $user)->first();

            if (! $vehicleModel) {
                return [
                    'success' => false,
                    'message' => 'Vehicle model not found',
                    'status' => 404,
                ];
            }

            $vehicleModel->load('vehicleBrand.vehicleType');

            return [
                'success' => true,
                'message' => 'Vehicle model retrieved successfully',
                'data' => $vehicleModel,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle model: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, User $user, array $data): array
    {
        try {
            $vehicleModel = $this->applyTenantScope(VehicleModel::where('id', $id), $user)->first();

            if (! $vehicleModel) {
                return [
                    'success' => false,
                    'message' => 'Vehicle model not found',
                    'status' => 404,
                ];
            }

            $vehicleModel->update($data);

            return [
                'success' => true,
                'message' => 'Vehicle model updated successfully',
                'data' => $vehicleModel->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update vehicle model: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id, User $user): array
    {
        try {
            $vehicleModel = $this->applyTenantScope(VehicleModel::where('id', $id), $user)->first();

            if (! $vehicleModel) {
                return [
                    'success' => false,
                    'message' => 'Vehicle model not found',
                    'status' => 404,
                ];
            }

            $vehicleModel->delete();

            return [
                'success' => true,
                'message' => 'Vehicle model deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete vehicle model: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
