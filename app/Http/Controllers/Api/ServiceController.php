<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->serviceService->index(
                $request->user()->org_id,
                $request->input('branch_id'),
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

    public function listByBranch(int $branchId): JsonResponse
    {
        try {
            $result = $this->serviceService->listByBranch($branchId);

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
                'branch_id' => ['nullable', 'exists:branches,id'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'base_price' => ['required', 'numeric', 'min:0'],
                'duration_minutes' => ['nullable', 'integer', 'min:1'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $user = $request->user();

            // ✅ Always take from auth
            $validated['org_id'] = $user->org_id;
            $validated['branch_id'] = $user->branch_id;

            $result = $this->serviceService->store($validated, $user->org_id);

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
            $result = $this->serviceService->show($id, $request->user()->org_id);

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
                // 'org_id' => ['sometimes', 'required', 'exists:organizations,id'],
                'branch_id' => ['nullable', 'exists:branches,id'],
                'name' => ['sometimes', 'required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'base_price' => ['sometimes', 'required', 'numeric', 'min:0'],
                'duration_minutes' => ['nullable', 'integer', 'min:1'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $user = $request->user();

            $validated['org_id'] = $user->org_id;
            $validated['branch_id'] = $user->branch_id;

            $result = $this->serviceService->update(
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
            $result = $this->serviceService->destroy($id, $request->user()->org_id);

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
