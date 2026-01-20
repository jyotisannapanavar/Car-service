<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\VehicleServicePricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class VehicleServicePricingController extends Controller
{
    public function __construct(
        protected VehicleServicePricingService $pricingService
    ) {}

    /**
     * @OA\Get(
     *     path="/pricing",
     *     summary="List pricing rules",
     *     description="Get paginated list of vehicle service pricing rules",
     *     operationId="pricingIndex",
     *     tags={"Pricing"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="branch_id", in="query", description="Filter by branch", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="service_id", in="query", description="Filter by service", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Pricing retrieved successfully"),
     *     @OA\Response(response=400, description="Branch ID is required"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/pricing",
     *     summary="Create pricing rule",
     *     description="Create a new vehicle service pricing rule",
     *     operationId="pricingStore",
     *     tags={"Pricing"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"branch_id", "service_id", "vehicle_type_id", "price"},
     *
     *         @OA\Property(property="branch_id", type="integer", example=1),
     *         @OA\Property(property="service_id", type="integer", example=1),
     *         @OA\Property(property="vehicle_type_id", type="integer", example=1),
     *         @OA\Property(property="vehicle_brand_id", type="integer", description="Optional for brand-specific pricing"),
     *         @OA\Property(property="vehicle_model_id", type="integer", description="Optional for model-specific pricing"),
     *         @OA\Property(property="price", type="number", format="float", example=49.99),
     *         @OA\Property(property="is_active", type="boolean", example=true)
     *     )),
     *
     *     @OA\Response(response=201, description="Pricing created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=409, description="Duplicate pricing rule"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
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

            $result = $this->pricingService->store(
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
     *     path="/pricing/{id}",
     *     summary="Get pricing rule",
     *     description="Get pricing rule details by ID",
     *     operationId="pricingShow",
     *     tags={"Pricing"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Pricing ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Pricing retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Pricing not found")
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/pricing/{id}",
     *     summary="Update pricing rule",
     *     description="Update pricing rule details",
     *     operationId="pricingUpdate",
     *     tags={"Pricing"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Pricing ID", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"branch_id", "service_id", "vehicle_type_id", "price"},
     *
     *         @OA\Property(property="branch_id", type="integer"),
     *         @OA\Property(property="service_id", type="integer"),
     *         @OA\Property(property="vehicle_type_id", type="integer"),
     *         @OA\Property(property="vehicle_brand_id", type="integer"),
     *         @OA\Property(property="vehicle_model_id", type="integer"),
     *         @OA\Property(property="price", type="number", format="float"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *
     *     @OA\Response(response=200, description="Pricing updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Pricing not found"),
     *     @OA\Response(response=409, description="Duplicate pricing rule"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/pricing/{id}",
     *     summary="Delete pricing rule",
     *     description="Soft delete a pricing rule",
     *     operationId="pricingDestroy",
     *     tags={"Pricing"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Pricing ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Pricing deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Pricing not found")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/pricing/lookup",
     *     summary="Lookup price",
     *     description="Find the best matching price for a service and vehicle combination. Uses hierarchical lookup: exact model -> brand level -> type level",
     *     operationId="pricingLookup",
     *     tags={"Pricing"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="branch_id", in="query", required=true, description="Branch ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="service_id", in="query", required=true, description="Service ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="vehicle_type_id", in="query", required=true, description="Vehicle Type ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="vehicle_brand_id", in="query", description="Vehicle Brand ID (optional)", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="vehicle_model_id", in="query", description="Vehicle Model ID (optional)", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Price found", @OA\JsonContent(
     *
     *         @OA\Property(property="success", type="boolean", example=true),
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="pricing", type="object"),
     *             @OA\Property(property="price", type="number", example=49.99),
     *             @OA\Property(property="match_type", type="string", enum={"exact_model", "brand_level", "type_level"})
     *         )
     *     )),
     *
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="No pricing found")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/pricing/by-service/{serviceId}",
     *     summary="Get pricing by service",
     *     description="Get all pricing rules for a specific service",
     *     operationId="pricingGetByService",
     *     tags={"Pricing"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="serviceId", in="path", required=true, description="Service ID", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="branch_id", in="query", description="Branch ID (uses user's branch if not provided)", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Pricing retrieved successfully"),
     *     @OA\Response(response=400, description="Branch ID is required"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
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
