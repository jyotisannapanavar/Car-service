<?php

namespace App\Services;

use App\Models\VehicleModel;
use Exception;

class VehicleModelService
{
    public function index(?int $vehicleBrandId = null, int $perPage = 15): array
    {
        try {
            $query = VehicleModel::query();

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
                'message' => 'Failed to retrieve vehicle models: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function listByBrand(int $vehicleBrandId): array
    {
        try {
            $vehicleModels = VehicleModel::where('vehicle_brand_id', $vehicleBrandId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return [
                'success' => true,
                'message' => 'Vehicle models retrieved successfully',
                'data' => $vehicleModels,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle models: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data, int $orgId, int $branchId): array
    {
        try {
            // Force org & branch from auth
            $data['org_id'] = $orgId;
            $data['branch_id'] = $branchId;

            // (Optional but recommended)
            // Ensure brand belongs to same org
            $brand = \App\Models\VehicleBrand::where('id', $data['vehicle_brand_id'])
                ->where('org_id', $orgId)
                ->first();

            if (!$brand) {
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

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create vehicle model: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }


    public function show(int $id): array
    {
        try {
            $vehicleModel = VehicleModel::find($id);

            if (!$vehicleModel) {
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
                'message' => 'Failed to retrieve vehicle model: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $vehicleModel = VehicleModel::find($id);

            if (!$vehicleModel) {
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
                'message' => 'Failed to update vehicle model: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id): array
    {
        try {
            $vehicleModel = VehicleModel::find($id);

            if (!$vehicleModel) {
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
                'message' => 'Failed to delete vehicle model: ' . $e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
