<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CustomerVehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerVehicleController extends Controller
{
    public function __construct(
        protected CustomerVehicleService $customerVehicleService
    ) {}

    public function index(Request $request, int $customerId): JsonResponse
    {
        try {
            $result = $this->customerVehicleService->index(
                $customerId,
                $request->user()->org_id,
                $request->input('per_page', 15)
            );

            $response = [
                'success' => $result['success'],
                'message' => $result['message'],
            ];

            if (isset($result['data'])) {
                $response['data'] = $result['data'];
            }
            if (isset($result['pagination'])) {
                $response['pagination'] = $result['pagination'];
            }

            return response()->json($response, $result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => ['required', 'exists:customers,id'],
                'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
                'vehicle_brand_id' => ['required', 'exists:vehicle_brands,id'],
                'vehicle_model_id' => ['required', 'exists:vehicle_models,id'],
                'registration_number' => ['nullable', 'string', 'max:50'],
                'color' => ['nullable', 'string', 'max:50'],
                'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
                'notes' => ['nullable', 'string'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->customerVehicleService->store(
                $validated,
                $request->user()->org_id
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
            ], $result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function show(Request $request, int $customerId, int $id): JsonResponse
    {
        try {
            $result = $this->customerVehicleService->show(
                $customerId,
                $id,
                $request->user()->org_id
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
            ], $result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function update(Request $request, int $customerId, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'customer_id' => ['required', 'exists:customers,id'],
                'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
                'vehicle_brand_id' => ['required', 'exists:vehicle_brands,id'],
                'vehicle_model_id' => ['required', 'exists:vehicle_models,id'],
                'registration_number' => ['nullable', 'string', 'max:50'],
                'color' => ['nullable', 'string', 'max:50'],
                'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
                'notes' => ['nullable', 'string'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->customerVehicleService->update(
                $customerId,
                $id,
                $request->user()->org_id,
                $validated
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
            ], $result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }

    public function destroy(Request $request, int $customerId, int $id): JsonResponse
    {
        try {
            $result = $this->customerVehicleService->destroy(
                $customerId,
                $id,
                $request->user()->org_id
            );

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null,
            ], $result['status']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
}
