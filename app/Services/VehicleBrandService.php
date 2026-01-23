<?php

namespace App\Services;

use App\Models\VehicleBrand;
use Exception;
use Illuminate\Http\Request;

class VehicleBrandService
{
    public function index(?int $vehicleTypeId = null, int $perPage = 15): array
    {
        try {
            $query = VehicleBrand::query();

            if ($vehicleTypeId) {
                $query->where('vehicle_type_id', $vehicleTypeId);
            }

            $vehicleBrands = $query->orderBy('name')->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Vehicle brands retrieved successfully',
                'data' => $vehicleBrands->items(),
                'pagination' => [
                    'current_page' => $vehicleBrands->currentPage(),
                    'total_pages' => $vehicleBrands->lastPage(),
                    'per_page' => $vehicleBrands->perPage(),
                    'total' => $vehicleBrands->total(),
                    'next_page_url' => $vehicleBrands->nextPageUrl(),
                    'prev_page_url' => $vehicleBrands->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle brands: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function listByType(int $vehicleTypeId): array
    {
        try {
            $vehicleBrands = VehicleBrand::where('vehicle_type_id', $vehicleTypeId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return [
                'success' => true,
                'message' => 'Vehicle brands retrieved successfully',
                'data' => $vehicleBrands,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle brands: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data, Request $request): array
    {

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')
                ->store('vehicle-brands', 'public');
        }
        try {
            $vehicleBrand = VehicleBrand::create($data);

            return [
                'success' => true,
                'message' => 'Vehicle brand created successfully',
                'data' => $vehicleBrand,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create vehicle brand: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function show(int $id): array
    {
        try {
            $vehicleBrand = VehicleBrand::find($id);

            if (! $vehicleBrand) {
                return [
                    'success' => false,
                    'message' => 'Vehicle brand not found',
                    'status' => 404,
                ];
            }

            $vehicleBrand->load(['vehicleType', 'vehicleModels']);

            return [
                'success' => true,
                'message' => 'Vehicle brand retrieved successfully',
                'data' => $vehicleBrand,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve vehicle brand: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $vehicleBrand = VehicleBrand::find($id);

            if (! $vehicleBrand) {
                return [
                    'success' => false,
                    'message' => 'Vehicle brand not found',
                    'status' => 404,
                ];
            }

            $vehicleBrand->update($data);

            return [
                'success' => true,
                'message' => 'Vehicle brand updated successfully',
                'data' => $vehicleBrand->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update vehicle brand: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $id): array
    {
        try {
            $vehicleBrand = VehicleBrand::find($id);

            if (! $vehicleBrand) {
                return [
                    'success' => false,
                    'message' => 'Vehicle brand not found',
                    'status' => 404,
                ];
            }

            $vehicleBrand->delete();

            return [
                'success' => true,
                'message' => 'Vehicle brand deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete vehicle brand: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
