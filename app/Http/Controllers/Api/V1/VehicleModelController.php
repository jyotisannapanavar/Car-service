<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VehicleModelRequest;
use App\Services\VehicleModelService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected VehicleModelService $vehicleModelService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $vehicleBrandId = $request->input('vehicle_brand_id');
            $vehicleModels = $this->vehicleModelService->getAll($vehicleBrandId, $perPage);

            return $this->paginatedResponse($vehicleModels, 'Vehicle models retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle models: '.$e->getMessage());
        }
    }

    public function listByBrand(int $vehicleBrandId): JsonResponse
    {
        try {
            $vehicleModels = $this->vehicleModelService->getAllByBrand($vehicleBrandId);

            return $this->successResponse($vehicleModels, 'Vehicle models retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle models: '.$e->getMessage());
        }
    }

    public function store(VehicleModelRequest $request): JsonResponse
    {
        try {
            $vehicleModel = $this->vehicleModelService->create($request->validated());

            return $this->createdResponse($vehicleModel, 'Vehicle model created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create vehicle model: '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $vehicleModel = $this->vehicleModelService->findById($id);

            if (! $vehicleModel) {
                return $this->notFoundResponse('Vehicle model not found');
            }

            $vehicleModel->load('vehicleBrand.vehicleType');

            return $this->successResponse($vehicleModel, 'Vehicle model retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve vehicle model: '.$e->getMessage());
        }
    }

    public function update(VehicleModelRequest $request, int $id): JsonResponse
    {
        try {
            $vehicleModel = $this->vehicleModelService->findById($id);

            if (! $vehicleModel) {
                return $this->notFoundResponse('Vehicle model not found');
            }

            $vehicleModel = $this->vehicleModelService->update($vehicleModel, $request->validated());

            return $this->successResponse($vehicleModel, 'Vehicle model updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update vehicle model: '.$e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $vehicleModel = $this->vehicleModelService->findById($id);

            if (! $vehicleModel) {
                return $this->notFoundResponse('Vehicle model not found');
            }

            $this->vehicleModelService->delete($vehicleModel);

            return $this->successResponse(null, 'Vehicle model deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete vehicle model: '.$e->getMessage());
        }
    }
}
