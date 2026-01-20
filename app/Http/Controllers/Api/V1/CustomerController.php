<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * @OA\Get(
     *     path="/customers",
     *     summary="List customers",
     *     description="Get paginated list of customers",
     *     operationId="customersIndex",
     *     tags={"Customers"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="per_page", in="query", description="Items per page", @OA\Schema(type="integer", default=15)),
     *     @OA\Parameter(name="branch_id", in="query", description="Filter by branch", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search", in="query", description="Search by name/phone/email", @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Customers retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $result = $this->customerService->index(
            $request->user()->org_id,
            $request->input('branch_id'),
            $request->input('search'),
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
     * @OA\Post(
     *     path="/customers",
     *     summary="Create customer",
     *     description="Create a new customer",
     *     operationId="customersStore",
     *     tags={"Customers"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"org_id", "name", "phone"},
     *
     *         @OA\Property(property="org_id", type="integer", example=1),
     *         @OA\Property(property="branch_id", type="integer"),
     *         @OA\Property(property="name", type="string", example="John Customer"),
     *         @OA\Property(property="email", type="string", format="email"),
     *         @OA\Property(property="phone", type="string", example="+1234567890"),
     *         @OA\Property(property="address", type="string"),
     *         @OA\Property(property="is_active", type="boolean", example=true)
     *     )),
     *
     *     @OA\Response(response=201, description="Customer created successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(CustomerRequest $request): JsonResponse
    {
        $result = $this->customerService->store(
            $request->validated(),
            $request->user()->org_id
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Get(
     *     path="/customers/{id}",
     *     summary="Get customer",
     *     description="Get customer details by ID",
     *     operationId="customersShow",
     *     tags={"Customers"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Customer retrieved successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer not found")
     * )
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $result = $this->customerService->show($id, $request->user()->org_id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Put(
     *     path="/customers/{id}",
     *     summary="Update customer",
     *     description="Update customer details",
     *     operationId="customersUpdate",
     *     tags={"Customers"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"org_id", "name", "phone"},
     *
     *         @OA\Property(property="org_id", type="integer"),
     *         @OA\Property(property="branch_id", type="integer"),
     *         @OA\Property(property="name", type="string"),
     *         @OA\Property(property="email", type="string", format="email"),
     *         @OA\Property(property="phone", type="string"),
     *         @OA\Property(property="address", type="string"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *
     *     @OA\Response(response=200, description="Customer updated successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(CustomerRequest $request, int $id): JsonResponse
    {
        $result = $this->customerService->update(
            $id,
            $request->user()->org_id,
            $request->validated()
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Delete(
     *     path="/customers/{id}",
     *     summary="Delete customer",
     *     description="Soft delete a customer",
     *     operationId="customersDestroy",
     *     tags={"Customers"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="id", in="path", required=true, description="Customer ID", @OA\Schema(type="integer")),
     *
     *     @OA\Response(response=200, description="Customer deleted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer not found")
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $result = $this->customerService->destroy($id, $request->user()->org_id);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }

    /**
     * @OA\Get(
     *     path="/customers/search",
     *     summary="Search customer by phone",
     *     description="Find a customer by phone number",
     *     operationId="customersSearchByPhone",
     *     tags={"Customers"},
     *     security={{"sanctum":{}}},
     *
     *     @OA\Parameter(name="phone", in="query", required=true, description="Phone number", @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Customer found"),
     *     @OA\Response(response=400, description="Phone number is required"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Customer not found")
     * )
     */
    public function searchByPhone(Request $request): JsonResponse
    {
        $result = $this->customerService->searchByPhone(
            $request->input('phone', ''),
            $request->user()->org_id
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
        ], $result['status']);
    }
}
