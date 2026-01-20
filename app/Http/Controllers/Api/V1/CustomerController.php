<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CustomerRequest;
use App\Services\CustomerService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $perPage = $request->input('per_page', 15);
            $branchId = $request->input('branch_id');
            $search = $request->input('search');

            $customers = $this->customerService->getAll(
                $user->organization_id,
                $branchId,
                $search,
                $perPage
            );

            return $this->paginatedResponse($customers, 'Customers retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve customers: '.$e->getMessage());
        }
    }

    public function store(CustomerRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            if (! $user->isAdmin() && $data['organization_id'] != $user->organization_id) {
                return $this->forbiddenResponse('You can only create customers for your organization');
            }

            $customer = $this->customerService->create($data);

            return $this->createdResponse($customer, 'Customer created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create customer: '.$e->getMessage());
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = $this->customerService->findByIdAndOrganization($id, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $customer->load(['organization', 'branch', 'vehicles.vehicleType', 'vehicles.vehicleBrand', 'vehicles.vehicleModel']);

            return $this->successResponse($customer, 'Customer retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve customer: '.$e->getMessage());
        }
    }

    public function update(CustomerRequest $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = $this->customerService->findByIdAndOrganization($id, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $customer = $this->customerService->update($customer, $request->validated());

            return $this->successResponse($customer, 'Customer updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update customer: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = $this->customerService->findByIdAndOrganization($id, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $this->customerService->delete($customer);

            return $this->successResponse(null, 'Customer deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete customer: '.$e->getMessage());
        }
    }

    public function searchByPhone(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $phone = $request->input('phone');

            if (! $phone) {
                return $this->errorResponse('Phone number is required', 400);
            }

            $customer = $this->customerService->findByPhone($phone, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $customer->load(['vehicles.vehicleType', 'vehicles.vehicleBrand', 'vehicles.vehicleModel']);

            return $this->successResponse($customer, 'Customer found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to search customer: '.$e->getMessage());
        }
    }
}
