<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->userService->index(
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

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'org_id' => ['required', 'exists:organizations,id'],
                'branch_id' => ['nullable', 'exists:branches,id'],
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email'),
                ],
                'phone' => ['nullable', 'string', 'max:20'],
                'role' => ['required', Rule::in(['admin', 'branch_manager', 'staff'])],
                'password' => ['required', 'string', 'min:8'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->userService->store(
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
            $result = $this->userService->show($id, $request->user()->org_id);

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
                'branch_id' => ['nullable', 'exists:branches,id'],
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users', 'email')->ignore($id),
                ],
                'phone' => ['nullable', 'string', 'max:20'],
                'role' => ['required', Rule::in(['admin', 'branch_manager', 'staff'])],
                'password' => ['nullable', 'string', 'min:8'],
                'is_active' => ['sometimes', 'boolean'],
            ]);

            $result = $this->userService->update(
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
            $currentUser = $request->user();
            $result = $this->userService->destroy($id, $currentUser->org_id, $currentUser->id);

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
