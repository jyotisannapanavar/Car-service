<?php

namespace App\Services;

use App\Models\User;
use App\Models\VehicleBrand;
use App\Traits\TenantScope;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleBrandService
{
    use TenantScope;

    public function index(User $user, ?int $vehicleTypeId = null, int $perPage = 15): array
    {
        try {
            $query = $this->applyTenantScope(VehicleBrand::query(), $user);

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

    public function listByType(User $user, int $vehicleTypeId): array
    {
        try {
            $query = $this->applyTenantScope(
                VehicleBrand::where('vehicle_type_id', $vehicleTypeId)->where('is_active', true),
                $user
            );
            $vehicleBrands = $query->orderBy('name')->get();

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

    public function show(int $id, User $user): array
    {
        try {
            $vehicleBrand = $this->applyTenantScope(VehicleBrand::where('id', $id), $user)->first();

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

    public function update(int $id, User $user, array $data): array
    {
        try {
            $vehicleBrand = $this->applyTenantScope(VehicleBrand::where('id', $id), $user)->first();

            if (! $vehicleBrand) {
                return [
                    'success' => false,
                    'message' => 'Vehicle brand not found',
                    'status' => 404,
                ];
            }

            if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
                if ($vehicleBrand->logo) {
                    Storage::disk('public')->delete($vehicleBrand->logo);
                }
                $data['logo'] = $data['logo']->store('vehicle-brands', 'public');
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

    public function destroy(int $id, User $user): array
    {
        try {
            $vehicleBrand = $this->applyTenantScope(VehicleBrand::where('id', $id), $user)->first();

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
