<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'phone')
                    ->where('org_id', $request->user()->org_id),
            ],
            'address' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ], [
            'phone.unique' => 'A customer with this phone number already exists in your organization.',
        ]);

        $result = $this->customerService->store(
            $validated,
            $request->user()
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
            $result = $this->customerService->show($id, $request->user()->org_id);

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
            $orgId = $request->input('org_id');

            $validated = $request->validate([
                'org_id' => ['required', 'exists:organizations,id'],
                'branch_id' => ['nullable', 'exists:branches,id'],
                'name' => ['required', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    Rule::unique('customers', 'phone')
                        ->where('org_id', $orgId)
                        ->ignore($id),
                ],
                'address' => ['nullable', 'string'],
                'is_active' => ['sometimes', 'boolean'],
            ], [
                'phone.unique' => 'A customer with this phone number already exists in your organization.',
            ]);

            $result = $this->customerService->update(
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

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->customerService->destroy($id, $request->user()->org_id);

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

    public function searchByPhone(Request $request): JsonResponse
    {
        try {
            $result = $this->customerService->searchByPhone(
                $request->input('phone', ''),
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
