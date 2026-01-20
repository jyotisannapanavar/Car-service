<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ServiceRequest;
use App\Services\ServiceService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ServiceService $serviceService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = $request->input('per_page', 15);
            $branchId = $request->input('branch_id');

            $services = $this->serviceService->getAll($user->organization_id, $branchId, $perPage);

            return $this->paginatedResponse($services, 'Services retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve services: '.$e->getMessage());
        }
    }

    public function listByBranch(int $branchId): JsonResponse
    {
        try {
            $services = $this->serviceService->getAllByBranch($branchId);

            return $this->successResponse($services, 'Services retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve services: '.$e->getMessage());
        }
    }

    public function store(ServiceRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            if (! $user->isAdmin() && $data['organization_id'] != $user->organization_id) {
                return $this->forbiddenResponse('You can only create services for your organization');
            }

            $service = $this->serviceService->create($data);

            return $this->createdResponse($service, 'Service created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create service: '.$e->getMessage());
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $service = $this->serviceService->findByIdAndOrganization($id, $user->organization_id);

            if (! $service) {
                return $this->notFoundResponse('Service not found');
            }

            $service->load(['organization', 'branch', 'vehicleServicePricing']);

            return $this->successResponse($service, 'Service retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve service: '.$e->getMessage());
        }
    }

    public function update(ServiceRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $service = $this->serviceService->findByIdAndOrganization($id, $user->organization_id);

            if (! $service) {
                return $this->notFoundResponse('Service not found');
            }

            $service = $this->serviceService->update($service, $request->validated());

            return $this->successResponse($service, 'Service updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update service: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $service = $this->serviceService->findByIdAndOrganization($id, $user->organization_id);

            if (! $service) {
                return $this->notFoundResponse('Service not found');
            }

            $this->serviceService->delete($service);

            return $this->successResponse(null, 'Service deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete service: '.$e->getMessage());
        }
    }
}
