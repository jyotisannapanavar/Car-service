<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PriceLookupRequest;
use App\Http\Requests\Api\V1\VehicleServicePricingRequest;
use App\Services\BranchService;
use App\Services\VehicleServicePricingService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleServicePricingController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected VehicleServicePricingService $pricingService,
        protected BranchService $branchService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $branchId = $request->input('branch_id', $user->branch_id);

            if (! $branchId) {
                return $this->errorResponse('Branch ID is required', 400);
            }

            $branch = $this->branchService->findByIdAndOrganization($branchId, $user->organization_id);
            if (! $branch) {
                return $this->forbiddenResponse('Branch does not belong to your organization');
            }

            $perPage = $request->input('per_page', 15);
            $serviceId = $request->input('service_id');

            $pricing = $this->pricingService->getAll($branchId, $serviceId, $perPage);

            return $this->paginatedResponse($pricing, 'Pricing retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve pricing: '.$e->getMessage());
        }
    }

    public function store(VehicleServicePricingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            $branch = $this->branchService->findByIdAndOrganization($data['branch_id'], $user->organization_id);
            if (! $branch) {
                return $this->forbiddenResponse('Branch does not belong to your organization');
            }

            $isDuplicate = $this->pricingService->checkDuplicatePricing(
                $data['branch_id'],
                $data['service_id'],
                $data['vehicle_type_id'],
                $data['vehicle_brand_id'] ?? null,
                $data['vehicle_model_id'] ?? null
            );

            if ($isDuplicate) {
                return $this->errorResponse('A pricing rule with these parameters already exists', 409);
            }

            $pricing = $this->pricingService->create($data);
            $pricing->load(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return $this->createdResponse($pricing, 'Pricing created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create pricing: '.$e->getMessage());
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $pricing = $this->pricingService->findById($id);

            if (! $pricing) {
                return $this->notFoundResponse('Pricing not found');
            }

            $branch = $this->branchService->findByIdAndOrganization($pricing->branch_id, $user->organization_id);
            if (! $branch) {
                return $this->forbiddenResponse('Pricing does not belong to your organization');
            }

            $pricing->load(['branch', 'service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return $this->successResponse($pricing, 'Pricing retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve pricing: '.$e->getMessage());
        }
    }

    public function update(VehicleServicePricingRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $pricing = $this->pricingService->findById($id);

            if (! $pricing) {
                return $this->notFoundResponse('Pricing not found');
            }

            $branch = $this->branchService->findByIdAndOrganization($pricing->branch_id, $user->organization_id);
            if (! $branch) {
                return $this->forbiddenResponse('Pricing does not belong to your organization');
            }

            $data = $request->validated();

            $isDuplicate = $this->pricingService->checkDuplicatePricing(
                $data['branch_id'],
                $data['service_id'],
                $data['vehicle_type_id'],
                $data['vehicle_brand_id'] ?? null,
                $data['vehicle_model_id'] ?? null,
                $id
            );

            if ($isDuplicate) {
                return $this->errorResponse('A pricing rule with these parameters already exists', 409);
            }

            $pricing = $this->pricingService->update($pricing, $data);
            $pricing->load(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return $this->successResponse($pricing, 'Pricing updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update pricing: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $pricing = $this->pricingService->findById($id);

            if (! $pricing) {
                return $this->notFoundResponse('Pricing not found');
            }

            $branch = $this->branchService->findByIdAndOrganization($pricing->branch_id, $user->organization_id);
            if (! $branch) {
                return $this->forbiddenResponse('Pricing does not belong to your organization');
            }

            $this->pricingService->delete($pricing);

            return $this->successResponse(null, 'Pricing deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete pricing: '.$e->getMessage());
        }
    }

    public function lookup(PriceLookupRequest $request): JsonResponse
    {
        try {
            $user = $request->user();
            $data = $request->validated();

            $branch = $this->branchService->findByIdAndOrganization($data['branch_id'], $user->organization_id);
            if (! $branch) {
                return $this->forbiddenResponse('Branch does not belong to your organization');
            }

            $pricing = $this->pricingService->lookupPrice(
                $data['branch_id'],
                $data['service_id'],
                $data['vehicle_type_id'],
                $data['vehicle_brand_id'] ?? null,
                $data['vehicle_model_id'] ?? null
            );

            if (! $pricing) {
                return $this->notFoundResponse('No pricing found for the specified parameters');
            }

            $pricing->load(['service', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return $this->successResponse([
                'pricing' => $pricing,
                'price' => $pricing->price,
                'match_type' => $this->getMatchType($pricing, $data),
            ], 'Price found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to lookup price: '.$e->getMessage());
        }
    }

    private function getMatchType(mixed $pricing, array $data): string
    {
        if ($pricing->vehicle_model_id && $pricing->vehicle_model_id == ($data['vehicle_model_id'] ?? null)) {
            return 'exact_model';
        }

        if ($pricing->vehicle_brand_id && $pricing->vehicle_brand_id == ($data['vehicle_brand_id'] ?? null)) {
            return 'brand_level';
        }

        return 'type_level';
    }

    public function getByService(Request $request, int $serviceId): JsonResponse
    {
        try {
            $user = $request->user();
            $branchId = $request->input('branch_id', $user->branch_id);

            if (! $branchId) {
                return $this->errorResponse('Branch ID is required', 400);
            }

            $branch = $this->branchService->findByIdAndOrganization($branchId, $user->organization_id);
            if (! $branch) {
                return $this->forbiddenResponse('Branch does not belong to your organization');
            }

            $pricing = $this->pricingService->getPricingByService($branchId, $serviceId);

            return $this->successResponse($pricing, 'Pricing retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve pricing: '.$e->getMessage());
        }
    }
}
