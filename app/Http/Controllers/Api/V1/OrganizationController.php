<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrganizationRequest;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class OrganizationController extends Controller
{
    public function __construct(
        protected OrganizationService $organizationService
    ) {}

    /**
     * @OA\Get(
     *     path="/organizations",
     *     summary="List all organizations",
     *     description="Get paginated list of all organizations (Admin only)",
     *     operationId="organizationsIndex",
     *     tags={"Organizations"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *
     *     @OA\Response(response=200, description="Organizations retrieved successfully", @OA\JsonContent(ref="#/components/schemas/PaginatedResponse")),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Admin only")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->organizationService->index($request->input('per_page', 15));

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
     * @OA\Post(
     *     path="/organizations",
     *     summary="Create organization",
     *     description="Create a new organization (Admin only)",
     *     operationId="organizationsStore",
     *     tags={"Organizations"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="ABC Car Wash"),
     *             @OA\Property(property="email", type="string", format="email", example="info@abccarwash.com"),
     *             @OA\Property(property="phone", type="string", example="+1234567890"),
     *             @OA\Property(property="address", type="string", example="123 Main St"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *
     *     @OA\Response(response=201, description="Organization created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Admin only"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(OrganizationRequest $request): JsonResponse
    {
        $result = $this->organizationService->store($request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Get(
     *     path="/organizations/{id}",
     *     summary="Get organization",
     *     description="Get organization details by ID (Admin only)",
     *     operationId="organizationsShow",
     *     tags={"Organizations"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Organization ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Organization retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Admin only"),
     *     @OA\Response(response=404, description="Organization not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $result = $this->organizationService->show($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Put(
     *     path="/organizations/{id}",
     *     summary="Update organization",
     *     description="Update organization details (Admin only)",
     *     operationId="organizationsUpdate",
     *     tags={"Organizations"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Organization ID", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name"},
     *
     *             @OA\Property(property="name", type="string", example="ABC Car Wash Updated"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="phone", type="string"),
     *             @OA\Property(property="address", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *
     *     @OA\Response(response=200, description="Organization updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Admin only"),
     *     @OA\Response(response=404, description="Organization not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(OrganizationRequest $request, int $id): JsonResponse
    {
        $result = $this->organizationService->update($id, $request->validated());

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Delete(
     *     path="/organizations/{id}",
     *     summary="Delete organization",
     *     description="Soft delete an organization (Admin only)",
     *     operationId="organizationsDestroy",
     *     tags={"Organizations"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Organization ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Organization deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden - Admin only"),
     *     @OA\Response(response=404, description="Organization not found")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->organizationService->destroy($id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }
}
