<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\VehicleBrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VehicleBrandController extends Controller
{
    public function __construct(
        protected VehicleBrandService $vehicleBrandService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->vehicleBrandService->index(
                $request->input('vehicle_type_id'),
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

    public function listByType(int $vehicleTypeId): JsonResponse
    {
        try {
            $result = $this->vehicleBrandService->listByType($vehicleTypeId);

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
                'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
                'name' => ['required', 'string', 'max:255'],
                'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

        $result = $this->vehicleBrandService->store($validated, $request);

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
            $result = $this->vehicleBrandService->show($id);

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
                'vehicle_type_id' => ['required', 'exists:vehicle_types,id'],
                'name' => ['required', 'string', 'max:255'],
                'logo' => ['nullable', 'string', 'max:255'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->vehicleBrandService->update($id, $validated);

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
            $result = $this->vehicleBrandService->destroy($id);

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
