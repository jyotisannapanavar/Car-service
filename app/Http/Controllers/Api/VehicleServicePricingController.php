<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VehicleServicePricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleServicePricingController extends Controller
{
    public function __construct(
        protected VehicleServicePricingService $pricingService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $result = $this->pricingService->index(
                $user->org_id,
                $request->input('branch_id', $user->branch_id),
                $request->input('service_id'),
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
                'service_id' => ['required', 'exists:services,id'],
                'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
                'vehicle_brand_id' => ['nullable', 'exists:vehicle_brands,id'],
                'vehicle_model_id' => ['nullable', 'exists:vehicle_models,id'],
                'price' => ['required', 'numeric', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
            ], [
                'branch_id.required' => 'Branch is required.',
                'branch_id.exists' => 'Selected branch does not exist.',
                'service_id.required' => 'Service is required.',
                'service_id.exists' => 'Selected service does not exist.',
                'vehicle_type_id.required' => 'Vehicle type is required.',
                'vehicle_type_id.exists' => 'Selected vehicle type does not exist.',
                'vehicle_brand_id.exists' => 'Selected vehicle brand does not exist.',
                'vehicle_model_id.exists' => 'Selected vehicle model does not exist.',
                'price.required' => 'Price is required.',
                'price.numeric' => 'Price must be a number.',
                'price.min' => 'Price cannot be negative.',
            ]);

            $user = $request->user();

            $result = $this->pricingService->store(
                $validated,
                $user->org_id,
                $user->branch_id
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

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->pricingService->show($id, $request->user()->org_id);

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

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'branch_id' => ['required', 'exists:branches,id'],
                'service_id' => ['required', 'exists:services,id'],
                'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
                'vehicle_brand_id' => ['nullable', 'exists:vehicle_brands,id'],
                'vehicle_model_id' => ['nullable', 'exists:vehicle_models,id'],
                'price' => ['required', 'numeric', 'min:0'],
                'is_active' => ['sometimes', 'boolean'],
            ], [
                'branch_id.required' => 'Branch is required.',
                'branch_id.exists' => 'Selected branch does not exist.',
                'service_id.required' => 'Service is required.',
                'service_id.exists' => 'Selected service does not exist.',
                'vehicle_type_id.required' => 'Vehicle type is required.',
                'vehicle_type_id.exists' => 'Selected vehicle type does not exist.',
                'vehicle_brand_id.exists' => 'Selected vehicle brand does not exist.',
                'vehicle_model_id.exists' => 'Selected vehicle model does not exist.',
                'price.required' => 'Price is required.',
                'price.numeric' => 'Price must be a number.',
                'price.min' => 'Price cannot be negative.',
            ]);

            $result = $this->pricingService->update(
                $id,
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

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->pricingService->destroy($id, $request->user()->org_id);

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

    public function lookup(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'branch_id' => ['required', 'exists:branches,id'],
                'service_id' => ['required', 'exists:services,id'],
                'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
                'vehicle_brand_id' => ['nullable', 'exists:vehicle_brands,id'],
                'vehicle_model_id' => ['nullable', 'exists:vehicle_models,id'],
            ]);

            $result = $this->pricingService->lookup(
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

    public function getByService(Request $request, int $serviceId): JsonResponse
    {
        try {
            $user = $request->user();
            $result = $this->pricingService->getByService(
                $serviceId,
                $user->org_id,
                $request->input('branch_id', $user->branch_id)
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
