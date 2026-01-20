<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BranchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(
        protected BranchService $branchService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->branchService->index(
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

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'org_id' => ['required', 'exists:organizations,id'],
                'name' => ['required', 'string', 'max:255'],
                'code' => ['nullable', 'string', 'max:50'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->branchService->store(
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

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->branchService->show($id, $request->user()->org_id);

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
                'org_id' => ['required', 'exists:organizations,id'],
                'name' => ['required', 'string', 'max:255'],
                'code' => ['nullable', 'string', 'max:50'],
                'email' => ['nullable', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->branchService->update(
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
            $result = $this->branchService->destroy($id, $request->user()->org_id);

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
