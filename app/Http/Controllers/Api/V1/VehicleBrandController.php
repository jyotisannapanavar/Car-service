<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VehicleBrandRequest;
use App\Services\VehicleBrandService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleBrandController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected VehicleBrandService $vehicleBrandService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $vehicleTypeId = $request->input('vehicle_type_id');
            $vehicleBrands = $this->vehicleBrandService->getAll($vehicleTypeId, $perPage);

            return $this->paginatedResponse($vehicleBrands, 'Vehicle brands retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle brands: '.$e->getMessage());
        }
    }

    public function listByType(int $vehicleTypeId): JsonResponse
    {
        try {
            $vehicleBrands = $this->vehicleBrandService->getAllByType($vehicleTypeId);

            return $this->successResponse($vehicleBrands, 'Vehicle brands retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle brands: '.$e->getMessage());
        }
    }

    public function store(VehicleBrandRequest $request): JsonResponse
    {
        try {
            $vehicleBrand = $this->vehicleBrandService->create($request->validated());

            return $this->createdResponse($vehicleBrand, 'Vehicle brand created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create vehicle brand: '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $vehicleBrand = $this->vehicleBrandService->findById($id);

            if (! $vehicleBrand) {
                return $this->notFoundResponse('Vehicle brand not found');
            }

            $vehicleBrand->load(['vehicleType', 'vehicleModels']);

            return $this->successResponse($vehicleBrand, 'Vehicle brand retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle brand: '.$e->getMessage());
        }
    }

    public function update(VehicleBrandRequest $request, int $id): JsonResponse
    {
        try {
            $vehicleBrand = $this->vehicleBrandService->findById($id);

            if (! $vehicleBrand) {
                return $this->notFoundResponse('Vehicle brand not found');
            }

            $vehicleBrand = $this->vehicleBrandService->update($vehicleBrand, $request->validated());

            return $this->successResponse($vehicleBrand, 'Vehicle brand updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update vehicle brand: '.$e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $vehicleBrand = $this->vehicleBrandService->findById($id);

            if (! $vehicleBrand) {
                return $this->notFoundResponse('Vehicle brand not found');
            }

            $this->vehicleBrandService->delete($vehicleBrand);

            return $this->successResponse(null, 'Vehicle brand deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete vehicle brand: '.$e->getMessage());
        }
    }
}
