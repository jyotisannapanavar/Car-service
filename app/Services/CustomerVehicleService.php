<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerVehicle;
use Exception;

class CustomerVehicleService
{
    public function index(int $customerId, int $orgId, int $perPage = 15): array
    {
        try {
            $customer = Customer::where('id', $customerId)
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $vehicles = CustomerVehicle::where('customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return [
                'success' => true,
                'message' => 'Customer vehicles retrieved successfully',
                'data' => $vehicles->items(),
                'pagination' => [
                    'current_page' => $vehicles->currentPage(),
                    'total_pages' => $vehicles->lastPage(),
                    'per_page' => $vehicles->perPage(),
                    'total' => $vehicles->total(),
                    'next_page_url' => $vehicles->nextPageUrl(),
                    'prev_page_url' => $vehicles->previousPageUrl(),
                ],
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve customer vehicles: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function store(array $data, int $orgId): array
    {
        try {
            $customer = Customer::where('id', $data['customer_id'])
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer does not belong to your organization',
                    'status' => 403,
                ];
            }

            $vehicle = CustomerVehicle::create($data);
            $vehicle->load(['vehicleType', 'vehicleBrand', 'vehicleModel']);

            return [
                'success' => true,
                'message' => 'Customer vehicle created successfully',
                'data' => $vehicle,
                'status' => 201,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to create customer vehicle: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function show(int $customerId, int $vehicleId, int $orgId): array
    {
        try {
            $customer = Customer::where('id', $customerId)
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $vehicle = CustomerVehicle::where('id', $vehicleId)
                ->where('customer_id', $customerId)
                ->first();

            if (! $vehicle) {
                return [
                    'success' => false,
                    'message' => 'Customer vehicle not found',
                    'status' => 404,
                ];
            }

            $vehicle->load(['customer', 'vehicleType', 'vehicleBrand', 'vehicleModel']);

            return [
                'success' => true,
                'message' => 'Customer vehicle retrieved successfully',
                'data' => $vehicle,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to retrieve customer vehicle: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function update(int $customerId, int $vehicleId, int $orgId, array $data): array
    {
        try {
            $customer = Customer::where('id', $customerId)
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $vehicle = CustomerVehicle::where('id', $vehicleId)
                ->where('customer_id', $customerId)
                ->first();

            if (! $vehicle) {
                return [
                    'success' => false,
                    'message' => 'Customer vehicle not found',
                    'status' => 404,
                ];
            }

            $vehicle->update($data);
            $vehicle->load(['vehicleType', 'vehicleBrand', 'vehicleModel']);

            return [
                'success' => true,
                'message' => 'Customer vehicle updated successfully',
                'data' => $vehicle->fresh(),
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update customer vehicle: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function destroy(int $customerId, int $vehicleId, int $orgId): array
    {
        try {
            $customer = Customer::where('id', $customerId)
                ->where('org_id', $orgId)
                ->first();

            if (! $customer) {
                return [
                    'success' => false,
                    'message' => 'Customer not found',
                    'status' => 404,
                ];
            }

            $vehicle = CustomerVehicle::where('id', $vehicleId)
                ->where('customer_id', $customerId)
                ->first();

            if (! $vehicle) {
                return [
                    'success' => false,
                    'message' => 'Customer vehicle not found',
                    'status' => 404,
                ];
            }

            $vehicle->delete();

            return [
                'success' => true,
                'message' => 'Customer vehicle deleted successfully',
                'data' => null,
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete customer vehicle: '.$e->getMessage(),
                'status' => 500,
            ];
        }
    }
}
