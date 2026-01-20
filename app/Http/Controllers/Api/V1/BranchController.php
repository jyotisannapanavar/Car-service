<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BranchRequest;
use App\Services\BranchService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected BranchService $branchService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = $request->input('per_page', 15);
            $branches = $this->branchService->getAll($user->organization_id, $perPage);

            return $this->paginatedResponse($branches, 'Branches retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve branches: '.$e->getMessage());
        }
    }

    public function store(BranchRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            if (! $user->isAdmin() && $data['organization_id'] != $user->organization_id) {
                return $this->forbiddenResponse('You can only create branches for your organization');
            }

            $branch = $this->branchService->create($data);

            return $this->createdResponse($branch, 'Branch created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create branch: '.$e->getMessage());
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $branch = $this->branchService->findByIdAndOrganization($id, $user->organization_id);

            if (! $branch) {
                return $this->notFoundResponse('Branch not found');
            }

            $branch->load(['organization', 'users', 'services']);

            return $this->successResponse($branch, 'Branch retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve branch: '.$e->getMessage());
        }
    }

    public function update(BranchRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $branch = $this->branchService->findByIdAndOrganization($id, $user->organization_id);

            if (! $branch) {
                return $this->notFoundResponse('Branch not found');
            }

            $branch = $this->branchService->update($branch, $request->validated());

            return $this->successResponse($branch, 'Branch updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update branch: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $branch = $this->branchService->findByIdAndOrganization($id, $user->organization_id);

            if (! $branch) {
                return $this->notFoundResponse('Branch not found');
            }

            $this->branchService->delete($branch);

            return $this->successResponse(null, 'Branch deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete branch: '.$e->getMessage());
        }
    }
}
