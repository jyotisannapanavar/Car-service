<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VehicleTypeRequest;
use App\Services\VehicleTypeService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleTypeController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected VehicleTypeService $vehicleTypeService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $vehicleTypes = $this->vehicleTypeService->getAll($perPage);

            return $this->paginatedResponse($vehicleTypes, 'Vehicle types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle types: '.$e->getMessage());
        }
    }

    public function list(): JsonResponse
    {
        try {
            $vehicleTypes = $this->vehicleTypeService->getAllWithoutPagination();

            return $this->successResponse($vehicleTypes, 'Vehicle types retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle types: '.$e->getMessage());
        }
    }

    public function store(VehicleTypeRequest $request): JsonResponse
    {
        try {
            $vehicleType = $this->vehicleTypeService->create($request->validated());

            return $this->createdResponse($vehicleType, 'Vehicle type created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create vehicle type: '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $vehicleType = $this->vehicleTypeService->findById($id);

            if (! $vehicleType) {
                return $this->notFoundResponse('Vehicle type not found');
            }

            $vehicleType->load('vehicleBrands');

            return $this->successResponse($vehicleType, 'Vehicle type retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle type: '.$e->getMessage());
        }
    }

    public function update(VehicleTypeRequest $request, int $id): JsonResponse
    {
        try {
            $vehicleType = $this->vehicleTypeService->findById($id);

            if (! $vehicleType) {
                return $this->notFoundResponse('Vehicle type not found');
            }

            $vehicleType = $this->vehicleTypeService->update($vehicleType, $request->validated());

            return $this->successResponse($vehicleType, 'Vehicle type updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update vehicle type: '.$e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $vehicleType = $this->vehicleTypeService->findById($id);

            if (! $vehicleType) {
                return $this->notFoundResponse('Vehicle type not found');
            }

            $this->vehicleTypeService->delete($vehicleType);

            return $this->successResponse(null, 'Vehicle type deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete vehicle type: '.$e->getMessage());
        }
    }
}
