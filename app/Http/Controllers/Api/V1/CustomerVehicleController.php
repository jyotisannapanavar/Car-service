<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CustomerVehicleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CustomerVehicleController extends Controller
{
    public function __construct(
        protected CustomerVehicleService $customerVehicleService
    ) {}

    /**
     * @OA\Get(
     *     path="/customers/{customer}/vehicles",
     *     summary="List customer vehicles",
     *     description="Get paginated list of vehicles for a customer",
     *     operationId="customerVehiclesIndex",
     *     tags={"Customer Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="customer", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *
     *     @OA\Response(response=200, description="Customer vehicles retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer not found")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/customer-vehicles",
     *     summary="Create customer vehicle",
     *     description="Add a new vehicle for a customer",
     *     operationId="customerVehiclesStore",
     *     tags={"Customer Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"customer_id", "vehicle_type_id", "vehicle_brand_id", "vehicle_model_id", "registration_number"},
     *
     *         @OA\Property(property="customer_id", type="integer", example=1),
     *         @OA\Property(property="vehicle_type_id", type="integer", example=1),
     *         @OA\Property(property="vehicle_brand_id", type="integer", example=1),
     *         @OA\Property(property="vehicle_model_id", type="integer", example=1),
     *         @OA\Property(property="registration_number", type="string", example="ABC-1234"),
     *         @OA\Property(property="color", type="string", example="White"),
     *         @OA\Property(property="year", type="integer", example=2023),
     *         @OA\Property(property="notes", type="string")
     *     )),
     *
     *     @OA\Response(response=201, description="Customer vehicle created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/customers/{customer}/vehicles/{vehicle}",
     *     summary="Get customer vehicle",
     *     description="Get customer vehicle details by ID",
     *     operationId="customerVehiclesShow",
     *     tags={"Customer Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="customer", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="vehicle", in="path", required=true, description="Vehicle ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Customer vehicle retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer or vehicle not found")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/customers/{customer}/vehicles/{vehicle}",
     *     summary="Update customer vehicle",
     *     description="Update customer vehicle details",
     *     operationId="customerVehiclesUpdate",
     *     tags={"Customer Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="customer", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="vehicle", in="path", required=true, description="Vehicle ID", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"customer_id", "vehicle_type_id", "vehicle_brand_id", "vehicle_model_id", "registration_number"},
     *
     *         @OA\Property(property="customer_id", type="integer"),
     *         @OA\Property(property="vehicle_type_id", type="integer"),
     *         @OA\Property(property="vehicle_brand_id", type="integer"),
     *         @OA\Property(property="vehicle_model_id", type="integer"),
     *         @OA\Property(property="registration_number", type="string"),
     *         @OA\Property(property="color", type="string"),
     *         @OA\Property(property="year", type="integer"),
     *         @OA\Property(property="notes", type="string")
     *     )),
     *
     *     @OA\Response(response=200, description="Customer vehicle updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer or vehicle not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/customers/{customer}/vehicles/{vehicle}",
     *     summary="Delete customer vehicle",
     *     description="Soft delete a customer vehicle",
     *     operationId="customerVehiclesDestroy",
     *     tags={"Customer Vehicles"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="customer", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="vehicle", in="path", required=true, description="Vehicle ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Customer vehicle deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer or vehicle not found")
     * )
     */
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
