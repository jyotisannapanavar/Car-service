<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CustomerVehicleRequest;
use App\Services\CustomerService;
use App\Services\CustomerVehicleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerVehicleController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected CustomerVehicleService $customerVehicleService,
        protected CustomerService $customerService
    ) {}

    public function index(Request $request, int $customerId): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = $this->customerService->findByIdAndOrganization($customerId, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $perPage = $request->input('per_page', 15);
            $vehicles = $this->customerVehicleService->getAll($customerId, $perPage);

            return $this->paginatedResponse($vehicles, 'Customer vehicles retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve customer vehicles: '.$e->getMessage());
        }
    }

    public function store(CustomerVehicleRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $user = $request->user();

            $customer = $this->customerService->findByIdAndOrganization($data['customer_id'], $user->organization_id);
            if (! $customer) {
                return $this->forbiddenResponse('Customer does not belong to your organization');
            }

            $vehicle = $this->customerVehicleService->create($data);
            $vehicle->load(['vehicleType', 'vehicleBrand', 'vehicleModel']);

            return $this->createdResponse($vehicle, 'Customer vehicle created successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create customer vehicle: '.$e->getMessage());
        }
    }

    public function show(Request $request, int $customerId, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = $this->customerService->findByIdAndOrganization($customerId, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $vehicle = $this->customerVehicleService->findByIdAndCustomer($id, $customerId);

            if (! $vehicle) {
                return $this->notFoundResponse('Customer vehicle not found');
            }

            $vehicle->load(['customer', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return $this->successResponse($vehicle, 'Customer vehicle retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve customer vehicle: '.$e->getMessage());
        }
    }

    public function update(CustomerVehicleRequest $request, int $customerId, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = $this->customerService->findByIdAndOrganization($customerId, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $vehicle = $this->customerVehicleService->findByIdAndCustomer($id, $customerId);

            if (! $vehicle) {
                return $this->notFoundResponse('Customer vehicle not found');
            }

            $vehicle = $this->customerVehicleService->update($vehicle, $request->validated());
            $vehicle->load(['vehicleType', 'vehicleBrand', 'vehicleModel']);

            return $this->successResponse($vehicle, 'Customer vehicle updated successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update customer vehicle: '.$e->getMessage());
        }
    }

    public function destroy(Request $request, int $customerId, int $id): JsonResponse
    {
        try {
            $user = $request->user();
            $customer = $this->customerService->findByIdAndOrganization($customerId, $user->organization_id);

            if (! $customer) {
                return $this->notFoundResponse('Customer not found');
            }

            $vehicle = $this->customerVehicleService->findByIdAndCustomer($id, $customerId);

            if (! $vehicle) {
                return $this->notFoundResponse('Customer vehicle not found');
            }

            $this->customerVehicleService->delete($vehicle);

            return $this->successResponse(null, 'Customer vehicle deleted successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete customer vehicle: '.$e->getMessage());
        }
    }
}
