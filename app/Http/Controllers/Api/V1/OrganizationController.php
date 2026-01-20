<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrganizationRequest;
use App\Services\OrganizationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected OrganizationService $organizationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $organizations = $this->organizationService->getAll($perPage);

            return $this->paginatedResponse($organizations, 'Organizations retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve organizations: '.$e->getMessage());
        }
    }

    public function store(OrganizationRequest $request): JsonResponse
    {
        try {
            $organization = $this->organizationService->create($request->validated());

            return $this->createdResponse($organization, 'Organization created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create organization: '.$e->getMessage());
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $organization = $this->organizationService->findById($id);

            if (! $organization) {
                return $this->notFoundResponse('Organization not found');
            }

            $organization->load(['branches', 'users']);

            return $this->successResponse($organization, 'Organization retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve organization: '.$e->getMessage());
        }
    }

    public function update(OrganizationRequest $request, int $id): JsonResponse
    {
        try {
            $organization = $this->organizationService->findById($id);

            if (! $organization) {
                return $this->notFoundResponse('Organization not found');
            }

            $organization = $this->organizationService->update($organization, $request->validated());

            return $this->successResponse($organization, 'Organization updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update organization: '.$e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $organization = $this->organizationService->findById($id);

            if (! $organization) {
                return $this->notFoundResponse('Organization not found');
            }

            $this->organizationService->delete($organization);

            return $this->successResponse(null, 'Organization deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete organization: '.$e->getMessage());
        }
    }
}
