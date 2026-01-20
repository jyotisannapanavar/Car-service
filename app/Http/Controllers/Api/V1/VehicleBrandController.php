<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VehicleBrandRequest;
use App\Services\VehicleBrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class VehicleBrandController extends Controller
{
    public function __construct(
        protected VehicleBrandService $vehicleBrandService
    ) {}

    /**
     * @OA\Get(
     *     path="/vehicle-brands",
     *     summary="List vehicle brands",
     *     description="Get paginated list of vehicle brands",
     *     operationId="vehicleBrandsIndex",
     *     tags={"Vehicle Brands"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="vehicle_type_id", in="query", description="Filter by vehicle type", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle brands retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
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
    }

    /**
     * @OA\Get(
     *     path="/vehicle-brands/by-type/{vehicleTypeId}",
     *     summary="List brands by vehicle type",
     *     description="Get all vehicle brands for a specific vehicle type",
     *     operationId="vehicleBrandsListByType",
     *     tags={"Vehicle Brands"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="vehicleTypeId", in="path", required=true, description="Vehicle Type ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle brands retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function listByType(int $vehicleTypeId): JsonResponse
    {
        $result = $this->vehicleBrandService->listByType($vehicleTypeId);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Post(
     *     path="/vehicle-brands",
     *     summary="Create vehicle brand",
     *     description="Create a new vehicle brand",
     *     operationId="vehicleBrandsStore",
     *     tags={"Vehicle Brands"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"vehicle_type_id", "name"},
     *
     *         @OA\Property(property="vehicle_type_id", type="integer", example=1),
     *         @OA\Property(property="name", type="string", example="Toyota"),
     *         @OA\Property(property="is_active", type="boolean", example=true)
     *     )),
     *
     *     @OA\Response(response=201, description="Vehicle brand created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(VehicleBrandRequest $request): JsonResponse
    {
        $result = $this->vehicleBrandService->store($request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Get(
     *     path="/vehicle-brands/{id}",
     *     summary="Get vehicle brand",
     *     description="Get vehicle brand details by ID",
     *     operationId="vehicleBrandsShow",
     *     tags={"Vehicle Brands"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Brand ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle brand retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle brand not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->vehicleBrandService->show($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Put(
     *     path="/vehicle-brands/{id}",
     *     summary="Update vehicle brand",
     *     description="Update vehicle brand details",
     *     operationId="vehicleBrandsUpdate",
     *     tags={"Vehicle Brands"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Brand ID", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"vehicle_type_id", "name"},
     *
     *         @OA\Property(property="vehicle_type_id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *
     *     @OA\Response(response=200, description="Vehicle brand updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle brand not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(VehicleBrandRequest $request, int $id): JsonResponse
    {
        $result = $this->vehicleBrandService->update($id, $request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Delete(
     *     path="/vehicle-brands/{id}",
     *     summary="Delete vehicle brand",
     *     description="Soft delete a vehicle brand",
     *     operationId="vehicleBrandsDestroy",
     *     tags={"Vehicle Brands"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Brand ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle brand deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle brand not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->vehicleBrandService->destroy($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }
}
