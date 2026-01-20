<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\VehicleTypeRequest;
use App\Services\VehicleTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class VehicleTypeController extends Controller
{
    public function __construct(
        protected VehicleTypeService $vehicleTypeService
    ) {}

    /**
     * @OA\Get(
     *     path="/vehicle-types",
     *     summary="List vehicle types",
     *     description="Get paginated list of vehicle types",
     *     operationId="vehicleTypesIndex",
     *     tags={"Vehicle Types"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *
     *     @OA\Response(response=200, description="Vehicle types retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->vehicleTypeService->index($request->input('per_page', 15));

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
     *     path="/vehicle-types/list",
     *     summary="List all vehicle types",
     *     description="Get all vehicle types without pagination",
     *     operationId="vehicleTypesList",
     *     tags={"Vehicle Types"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Response(response=200, description="Vehicle types retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function list(): JsonResponse
    {
        $result = $this->vehicleTypeService->list();

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Post(
     *     path="/vehicle-types",
     *     summary="Create vehicle type",
     *     description="Create a new vehicle type",
     *     operationId="vehicleTypesStore",
     *     tags={"Vehicle Types"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"name"},
     *
     *         @OA\Property(property="name", type="string", example="Sedan"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_active", type="boolean", example=true)
     *     )),
     *
     *     @OA\Response(response=201, description="Vehicle type created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(VehicleTypeRequest $request): JsonResponse
    {
        $result = $this->vehicleTypeService->store($request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Get(
     *     path="/vehicle-types/{id}",
     *     summary="Get vehicle type",
     *     description="Get vehicle type details by ID",
     *     operationId="vehicleTypesShow",
     *     tags={"Vehicle Types"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Type ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle type retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle type not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->vehicleTypeService->show($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Put(
     *     path="/vehicle-types/{id}",
     *     summary="Update vehicle type",
     *     description="Update vehicle type details",
     *     operationId="vehicleTypesUpdate",
     *     tags={"Vehicle Types"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Type ID", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"name"},
     *
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *
     *     @OA\Response(response=200, description="Vehicle type updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle type not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(VehicleTypeRequest $request, int $id): JsonResponse
    {
        $result = $this->vehicleTypeService->update($id, $request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Delete(
     *     path="/vehicle-types/{id}",
     *     summary="Delete vehicle type",
     *     description="Soft delete a vehicle type",
     *     operationId="vehicleTypesDestroy",
     *     tags={"Vehicle Types"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Vehicle Type ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Vehicle type deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Vehicle type not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->vehicleTypeService->destroy($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }
}
