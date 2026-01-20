<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\VehicleModelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleModelController extends Controller
{
    public function __construct(
        protected VehicleModelService $vehicleModelService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->vehicleModelService->index(
                $request->input('vehicle_brand_id'),
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

    public function listByBrand(int $vehicleBrandId): JsonResponse
    {
        try {
            $result = $this->vehicleModelService->listByBrand($vehicleBrandId);

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

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'vehicle_brand_id' => ['required', 'exists:vehicle_brands,id'],
                'name' => ['required', 'string', 'max:255'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->vehicleModelService->store($validated);

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

    public function show(int $id): JsonResponse
    {
        try {
            $result = $this->vehicleModelService->show($id);

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
                'vehicle_brand_id' => ['required', 'exists:vehicle_brands,id'],
                'name' => ['required', 'string', 'max:255'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->vehicleModelService->update($id, $validated);

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

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->vehicleModelService->destroy($id);

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
