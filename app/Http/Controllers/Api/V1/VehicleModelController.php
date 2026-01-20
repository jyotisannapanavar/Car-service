<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VehicleModelRequest;
use App\Services\VehicleModelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class VehicleModelController extends Controller
{
    public function __construct(
        protected VehicleModelService $vehicleModelService
    ) {}

    /**
     * @OA\Get(
     *     path="/vehicle-models",
     *     summary="List vehicle models",
     *     description="Get paginated list of vehicle models",
     *     operationId="vehicleModelsIndex",
     *     tags={"Vehicle Models"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="vehicle_brand_id", in="query", description="Filter by vehicle brand", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle models retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
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
    }

    /**
     * @OA\Get(
     *     path="/vehicle-models/by-brand/{vehicleBrandId}",
     *     summary="List models by vehicle brand",
     *     description="Get all vehicle models for a specific vehicle brand",
     *     operationId="vehicleModelsListByBrand",
     *     tags={"Vehicle Models"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="vehicleBrandId", in="path", required=true, description="Vehicle Brand ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle models retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function listByBrand(int $vehicleBrandId): JsonResponse
    {
        $result = $this->vehicleModelService->listByBrand($vehicleBrandId);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Post(
     *     path="/vehicle-models",
     *     summary="Create vehicle model",
     *     description="Create a new vehicle model",
     *     operationId="vehicleModelsStore",
     *     tags={"Vehicle Models"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"vehicle_brand_id", "name"},
     *
     *         @OA\Property(property="vehicle_brand_id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Camry"),
     *         @OA\Property(property="is_active", type="boolean", example=true)
     *     )),
     *
     *     @OA\Response(response=201, description="Vehicle model created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(VehicleModelRequest $request): JsonResponse
    {
        $result = $this->vehicleModelService->store($request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Get(
     *     path="/vehicle-models/{id}",
     *     summary="Get vehicle model",
     *     description="Get vehicle model details by ID",
     *     operationId="vehicleModelsShow",
     *     tags={"Vehicle Models"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Model ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle model retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle model not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->vehicleModelService->show($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Put(
     *     path="/vehicle-models/{id}",
     *     summary="Update vehicle model",
     *     description="Update vehicle model details",
     *     operationId="vehicleModelsUpdate",
     *     tags={"Vehicle Models"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Model ID", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"vehicle_brand_id", "name"},
     *
     *         @OA\Property(property="vehicle_brand_id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *
     *     @OA\Response(response=200, description="Vehicle model updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle model not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(VehicleModelRequest $request, int $id): JsonResponse
    {
        $result = $this->vehicleModelService->update($id, $request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Delete(
     *     path="/vehicle-models/{id}",
     *     summary="Delete vehicle model",
     *     description="Soft delete a vehicle model",
     *     operationId="vehicleModelsDestroy",
     *     tags={"Vehicle Models"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Model ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle model deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle model not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->vehicleModelService->destroy($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }
}
